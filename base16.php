<?php
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
		echo "Base16 Builder PHP\n";
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

		// Loop scheme directories
		foreach ($sch_list as $sch_name => $sch_url) {

			$sch_names = ['scheme-dark', 'scheme-light'];

			// Loop scheme files
			foreach ($sch_names as $sch_file) {

				$sch_file = "schemes/$sch_name/$sch_file.yaml";

				if (file_exists($sch_file)) {

					$sch_data = $builder->parse($sch_file);
					$tpl_data = $builder->buildTemplateData($sch_data);
				
					// Loop templates
					foreach ($tpl_list as $tpl_name => $tpl_url) {

						$tpl_conf = $builder->parse(
							"templates/$tpl_name/template-config.yaml");

						$file_path = "templates/$tpl_name/" 
							. @$tpl_conf['output'];

						$file_name = 'base16-'.$tpl_data['scheme-slug']
							. $tpl_conf['extension'];
						
						$render = $builder->renderTemplate(
							"templates/$tpl_name/template.mustache", $tpl_data);

						$builder->writeFile($file_path.$file_name, $render);

						echo "Built $file_name\n";

					}

				}
			}
		}
		break;
}