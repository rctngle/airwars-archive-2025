/* global __dirname */

const path = require('path');
// const LiveReloadPlugin = require('webpack-livereload-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = [{
	entry: {
		scripts: './src/javascript/scripts.js',
		admin: './src/javascript/admin.js',
		
		adminstyles: './src/sass/admin.scss',
		editor: './src/sass/editor.scss',
		screen: './src/sass/screen.scss',
		source: './src/sass/source.scss',
	},
	output: {
		filename: 'scripts/[name].js',
		path: path.resolve(__dirname, './build')
	},  
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
				options: {
					presets: [
						'@babel/preset-env',
						'@babel/preset-react'
					]
				}
			},
			{
				test: /\.s[ac]ss$/i,
				exclude: /node_modules/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'postcss-loader',
					'sass-loader',
				],
			},
			{
				test: /\.(woff|woff2|eot|ttf|otf)$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: '[name].[ext]',
							outputPath: '../build/fonts',
							publicPath: '../../build/fonts',
						}
					}
				]				
			},
			{
				test: /\.(png|svg|jpg|gif|eot)$/,
				use: [
					{ 
						loader: 'url-loader', 
						options: { 
							outputPath: '../build/images',
							publicPath: '../../build/images',
							limit: 8192 
						}
					} 
				]
			}
			// {
			// 	test: /\.(eot|gif|otf|png|svg|ttf|woff)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
			// 	use: [ 'file-loader' ],
			// },
		]
	},
	plugins: [
		// new LiveReloadPlugin(),
		new MiniCssExtractPlugin({
			filename: 'styles/[name].css',
			chunkFilename: 'styles/[name].css'
		})
	]
}];