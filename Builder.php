<?php
/**
 * Base16 Builder CLI (Command Line Interface)
 */

// Source paths
$sources_list = 'sources.yaml';
$schemes_list = 'sources/schemes/list.yaml';
$templates_list = 'sources/templates/list.yaml';

$loader = require __DIR__ . '/vendor/autoload.php';

use Base16\Builder;

$builder = new Builder;

// Parse sources lists
$src_list = $builder->parse($sources_list);
if (file_exists($schemes_list)) $sch_list = $builder->parse($schemes_list);
if (file_exists($schemes_list)) $tpl_list = $builder->parse($templates_list);

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
		$builder->updateSources($src_list, 'sources');

		// Parse source lists incase the sources have just been fetched
		if (file_exists($schemes_list)) {
			$sch_list = $builder->parse($schemes_list);
		}

		if (file_exists($schemes_list)) {
			$tpl_list = $builder->parse($templates_list);
		}

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
							"templates/$tpl_name/templates/$tpl_file.mustache",
							 $tpl_data);

						$builder->writeFile($file_path, $file_name, $render);

						echo "Built $file_name\n";
					}

				}

			}
		}
		break;
}