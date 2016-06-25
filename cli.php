<?php
/**
 * Base16 Builder CLI (Command Line Interface)
 */

$loader = require __DIR__ . '/vendor/autoload.php';

use Base16\Builder;

$builder = new Builder;

$sch_list = $builder->parse('schemes.yaml');
$tpl_list = $builder->parse('templates.yaml');

/**
 * Switches between functions based on supplied argument
 */
switch (@$argv[1]) {

	/**
	* Displays a help message
	*/
	case '-h':
		echo "Base16 Builder PHP CLI\n";
		echo "https://github.com/chriskempson/base16-builder-php\n";
		break;
	
	/**
	* Updates template and scheme sources
	*/
	case 'update':
		$builder->updateSources($sch_list, 'schemes');
		$builder->updateSources($tpl_list, 'templates');
		break;

	/**
	* Build all themes and schemes
	*/
	default:

		// Loop scheme repositories
		foreach ($sch_list as $sch_name => $sch_url) {

			// Loop scheme files
			foreach (glob("schemes/$sch_name/*.yaml") as $sch_file) {

				$sch_data = $builder->parse($sch_file);
				$tpl_data = $builder->buildTemplateData($sch_data);
			
				// Loop templates repositories
				foreach ($tpl_list as $tpl_name => $tpl_url) {

					$tpl_confs = $builder->parse(
						"templates/$tpl_name/templates/config.yaml");

					// Loop template files
					foreach ($tpl_confs as $tpl_file => $tpl_conf) {

						$file_path = "templates/$tpl_name/" 
							. @$tpl_conf['output'];

						$file_name = 'base16-'.$tpl_data['scheme-slug']
							. $tpl_conf['extension'];
						
						$render = $builder->renderTemplate(
							"templates/$tpl_name/templates/$tpl_file.mustache", $tpl_data);

						$builder->writeFile($file_path, $file_name, $render);

						echo "Built $file_name\n";
					}

				}

			}
		}
		break;
}