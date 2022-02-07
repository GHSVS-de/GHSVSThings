// Noch nicht als helper.minifyCSS getestet!
// Aus plg_content_prismhighlighterghsvs übernommen, wo nicht mehr nötig. Dort war ein Kommentar: "Nicht async!" (keine Ahnung).
/*
Der Aufruf fand dort so statt:
	// ### Minify CSS - START
	let rootPath = `${__dirname}/package/media/css`;

	await recursive(rootPath, ['!*.+(css)']).then(
		function(files) {
			minifyCSS(files, rootPath);
		},
		function(error) {
			console.error("something exploded", error);
		}
	);
	// ### Minify CSS - ENDE
*/

var CleanCSS = require('clean-css');

module.exports.minifyCSS = async (files, rootPath) =>
{
	for (const file of files)
	{
		if (
			fse.existsSync(file) && fse.lstatSync(file).isFile() &&
			!file.endsWith('/backend.css') &&
			file.endsWith('.css') &&
			!file.endsWith('.min.css')
			//&& !fse.existsSync(file.replace(`.css`, `.min.css`))
		){
			const content = fse.readFileSync(file, { encoding: 'utf8' });
			var options = { /* options */ };
			const output = new CleanCSS(options).minify(content);

			if (output.errors.length)
			{
				console.log(chalk.redBright(`Errors in minifyCSS()!!!!!!!!!!`));
				console.log(chalk.redBright(file));
				console.log(output.errors);
				process.exit(1);
			}

			if (output.warnings.length)
			{
				console.log(chalk.redBright(`Warnings in minifyCSS()!!!!!!!!!!`));
				console.log(chalk.redBright(file));
				console.log(output.warnings);
			}
			let outputFile = file.replace('.css', '.min.css');
			fse.writeFileSync(outputFile,output.styles, { encoding: 'utf8'});
			outputFile = outputFile.replace(rootPath, '');
			console.log(chalk.greenBright(`Minified: ${outputFile}`));
		}
	}
}
