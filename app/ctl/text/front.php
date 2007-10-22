<?
/**
 * front controller modulu tekstowego
 */
class UFctl_Text_Front
extends UFctl {

	protected function getAlias($number=0) {
		$segments = $this->_srv->get('req')->segments();
		for ($i=0; $i<$number; ++$i) {
			array_shift($segments);
		}
		return implode('/', $segments);
	}

	public function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();
		if (0 == $segCount) {
			$get->view = 'list';
		} else {
			switch ($req->segment(1)) {
				case ':add':
					$get->view = 'admin/add';
					break;
				case ':edit':
					if (1 == $segCount) {
						$get->view = 'admin/list';
					} else {
						$get->view = 'admin/edit';
						$get->alias = $this->getAlias(1);
					}
					break;
				case ':del':
					$get->view = 'admin/del';
					$get->alias = $this->getAlias(1);
					break;
				default:
					$get->view = 'show';
					$get->alias = $this->getAlias();
					break;
			}
		}
	}

	public function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
		$acl = $this->_srv->get('acl');

		if ('admin/add' == $get->view && $post->is('textAdd') && $acl->text('text', 'add')) {
			if ($post->is('textPreview')) {
				$act = 'Text_AddPreview';
			} else {
				$act = 'Text_Add';
			}
		} elseif ('admin/edit' == $get->view && $post->is('textEdit') && $acl->text('text', 'edit')) {
			if ($post->is('textPreview')) {
				$act = 'Text_EditPreview';
			} else {
				$act = 'Text_Edit';
			}
		} elseif ('admin/del' == $get->view && $post->is('textDel') && $acl->text('text', 'del')) {
			$act = 'Text_Del';
		}

		if (isset($act)) {
			$action = 'Text_'.$act;
		}

		return $action;
	}

	public function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post= $req->post;
		$msg = $this->_srv->get('msg');
		$acl = $this->_srv->get('acl');

		switch ($get->view) {
			case 'show':
				return 'Text_TextShow';
			case 'list':
				return 'Text_TextList';
			case 'admin/list':
				return 'Text_TextAdmin';
			case 'admin/add':
				if ($post->is('textPreview')) {
					return 'Text_TextAddPreview';
				} else {
					return 'Text_TextAdd';
				}
			case 'admin/edit':
				if ($get->is('changedAlias')) {
					return 'Text_TextAdmin';
				} elseif ($post->is('textPreview')) {
					return 'Text_TextEditPreview';
				} else {
					return 'Text_TextEdit';
				}
			case 'admin/del':
				if ($get->is('changedAlias')) {
					return 'Text_TextAdmin';
				} else {
					return 'Text_TextDel';
				}
			default:
				return 'Text_Error404';
		}
	}
}
