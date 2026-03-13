import { config } from "dotenv";
config({ path: new URL("../../.env", import.meta.url) });
import { createReadStream, createWriteStream, existsSync, statSync, readdirSync } from "fs";
import { createGzip } from "zlib";
import { pipeline } from "stream/promises";
import { S3Client, HeadObjectCommand } from "@aws-sdk/client-s3";
import { Upload } from "@aws-sdk/lib-storage";

const DB_DIR = "setup/database";
const BUCKET = "airwars-greenhost-backup";

const required = (name) => {
  const val = process.env[name];
  if (!val) {
    console.error(`Error: Set ${name} environment variable`);
    process.exit(1);
  }
  return val;
};

const accountId = required("CLOUDFLARE_ACCOUNT_ID");
const accessKeyId = required("AWS_ACCESS_KEY_ID");
const secretAccessKey = required("AWS_SECRET_ACCESS_KEY");

const client = new S3Client({
  region: "auto",
  endpoint: `https://${accountId}.r2.cloudflarestorage.com`,
  credentials: { accessKeyId, secretAccessKey },
});

async function existsOnR2(key) {
  try {
    await client.send(new HeadObjectCommand({ Bucket: BUCKET, Key: key }));
    return true;
  } catch {
    return false;
  }
}

const sqlFiles = readdirSync(DB_DIR).filter((f) => f.endsWith(".sql"));

if (sqlFiles.length === 0) {
  console.log(`No .sql files found in ${DB_DIR}`);
  process.exit(0);
}

console.log(`Found ${sqlFiles.length} SQL file(s)\n`);

for (const file of sqlFiles) {
  const sqlPath = `${DB_DIR}/${file}`;
  const gzPath = `${sqlPath}.gz`;
  const objectKey = `${file}.gz`;

  console.log(`--- ${file} ---`);

  // Check R2
  if (await existsOnR2(objectKey)) {
    console.log(`  Already on R2, skipping\n`);
    continue;
  }

  // Compress
  if (!existsSync(gzPath)) {
    console.log(`  Compressing...`);
    await pipeline(
      createReadStream(sqlPath),
      createGzip(),
      createWriteStream(gzPath)
    );
  }
  const sizeMB = (statSync(gzPath).size / 1024 / 1024).toFixed(1);
  console.log(`  Compressed size: ${sizeMB} MB`);

  // Upload
  console.log(`  Uploading...`);
  const upload = new Upload({
    client,
    params: {
      Bucket: BUCKET,
      Key: objectKey,
      Body: createReadStream(gzPath),
      ContentType: "application/gzip",
    },
  });

  upload.on("httpUploadProgress", (progress) => {
    if (progress.loaded) {
      const mb = (progress.loaded / 1024 / 1024).toFixed(1);
      process.stdout.write(`\r  ${mb} MB uploaded...`);
    }
  });

  await upload.done();
  console.log(`\n  Done!\n`);
}

console.log("All files processed.");
