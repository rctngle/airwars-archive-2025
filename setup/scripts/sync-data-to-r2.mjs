import { config } from "dotenv";
config({ path: new URL("../../.env", import.meta.url) });
import { createReadStream, readdirSync, statSync } from "fs";
import { S3Client, HeadObjectCommand } from "@aws-sdk/client-s3";
import { Upload } from "@aws-sdk/lib-storage";

import { fileURLToPath } from "url";
import { dirname, resolve } from "path";

const __dirname = dirname(fileURLToPath(import.meta.url));
const DATA_DIR = resolve(__dirname, "../data");
const BUCKET = process.env.R2_BUCKET || "airwars-greenhost-backup";
const R2_PREFIX = "data/";

const required = (name) => {
  const val = process.env[name];
  if (!val) {
    console.error(`Error: Set ${name} environment variable`);
    process.exit(1);
  }
  return val;
};

const accountId = required("CLOUDFLARE_ACCOUNT_ID");
const accessKeyId = required("R2_ACCESS_KEY_ID");
const secretAccessKey = required("R2_SECRET_ACCESS_KEY");

const client = new S3Client({
  region: "auto",
  endpoint: `https://${accountId}.r2.cloudflarestorage.com`,
  credentials: { accessKeyId, secretAccessKey },
});

async function getR2Size(key) {
  try {
    const res = await client.send(new HeadObjectCommand({ Bucket: BUCKET, Key: key }));
    return res.ContentLength;
  } catch {
    return null;
  }
}

const files = readdirSync(DATA_DIR);

if (files.length === 0) {
  console.log(`No files found in ${DATA_DIR}`);
  process.exit(0);
}

console.log(`Found ${files.length} file(s) in ${DATA_DIR}\n`);

let uploaded = 0;
let skipped = 0;

for (const file of files) {
  const localPath = `${DATA_DIR}/${file}`;
  const localSize = statSync(localPath).size;
  const objectKey = `${R2_PREFIX}${file}`;

  const r2Size = await getR2Size(objectKey);

  if (r2Size !== null && r2Size === localSize) {
    skipped++;
    continue;
  }

  const sizeMB = (localSize / 1024 / 1024).toFixed(2);
  console.log(`Uploading ${file} (${sizeMB} MB)...`);

  const contentType = file.endsWith(".json") ? "application/json"
    : file.endsWith(".csv") ? "text/csv"
    : "application/octet-stream";

  const upload = new Upload({
    client,
    params: {
      Bucket: BUCKET,
      Key: objectKey,
      Body: createReadStream(localPath),
      ContentType: contentType,
    },
  });

  await upload.done();
  uploaded++;
}

console.log(`\nDone. Uploaded: ${uploaded}, Skipped (unchanged): ${skipped}`);
