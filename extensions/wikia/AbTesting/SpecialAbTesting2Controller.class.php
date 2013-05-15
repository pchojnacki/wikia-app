<?php
/**
 * @author Piotr Bablok
 */

class SpecialAbTesting2Controller extends WikiaSpecialPageController {

	public function __construct() {
		parent::__construct('AbTesting2', 'abtestpanel', false);
	}

	public function index() {
		if ( !$this->wg->User->isAllowed( 'abtestpanel' ) ) {
			$this->skipRendering();
			throw new PermissionsError( 'abtestpanel' );
		}

		$this->getResponse()->addModuleStyles('wikia.ext.abtesting.edit2.styles');
		$this->getResponse()->addModules('wikia.ext.abtesting.edit2');
		$this->setHeaders();

		$abData = new AbTestingData();
		$experiments = $abData->getAll();
		foreach ($experiments as &$exp) {
			$exp['actions'] = array();
			// add "Edit experiment" button
			if ( $exp['status'] == AbTesting::STATUS_ACTIVE ) {
				$exp['actions'][] = array(
					'cmd' => 'cancel-experiment',
					'class' => 'secondary',
					'spriteclass' => '',
					'text' => $this->wf->msg('abtesting-cancel-button'),
				);
			}
			$exp['actions'][] = array(
				'cmd' => 'edit-experiment',
				'class' => '',
				'spriteclass' => 'edit-pencil sprite',
				'text' => $this->wf->msg('abtesting-edit-button'),
			);
		}
		$this->setVal( 'experiments', $experiments );

		$actions = array();
		$actions[] = array(
			'cmd' => 'add-experiment',
			'class' => 'add sprite-small',
			'text' => $this->wf->msg('abtesting-create-experiment'),
		);
		$this->setVal( 'actions', $actions );

	}

}