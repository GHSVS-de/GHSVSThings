/**
	* called when after a module is rendered on the back- or front-end.
*/
public function onAfterRenderModule(&$module, $attribs)
{
	// Add frontend module editing button if activated.
	if ($this->app->isClient('site'))
	{
		if (
			Bs3GhsvsFormHelper::getActiveXml('Module', $this->params, array(1))

			// Global switch:
			&& $this->params->get('frontendEditingOn', 0) === 1

			// Check setting of myforms/module.xml:
			&& strpos($module->params, '"frontendEditingOn":1') !==false

			// Check the user rights before we do regex shit:
			&& Factory::getUser()->authorise(
				'module.edit.frontend', 'com_modules.module.' . $module->id
			)

			&& trim($module->content)
		){
			$editBtn = LayoutHelper::render('ghsvs.frontediting_modules_in_article',
				array('module' => $module)
			);

			// Find first HTML tag. Normally a <div>.
			$muster = '/^(\s*<(?:div|span|nav|ul|ol|h\d|section|aside|nav|address|article) [^>]*>)/m';

			// Add editing link directly after the first HTML tag.
			$module->content = preg_replace($muster, '\\0' . $editBtn, $module->content, 1);
		}
	}
}
