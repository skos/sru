<?
/**
 * wysłanie maila z ostatnimi akcjami na karach
 */
class UFact_SruApi_Penalty_Timeline
extends UFact {

	const PREFIX = 'penaltyTimeline';

	public function go() {
		try {
			$box = UFra::factory('UFbox_SruApi');
			$sender = UFra::factory('UFlib_Sender');
			$title = $box->apiPenaltiesTimelineMailTitle();

			try {
				$added = UFra::factory('UFbean_SruAdmin_PenaltyList');
				$added->listLastAdded(null, null, null, 24 * 60 * 60);
			} catch (UFex_Dao_NotFound $e) {
				$added = null;
			}
			try {
				$modified = UFra::factory('UFbean_SruAdmin_PenaltyList');
				$modified->listLastModified(null, null, null, 24 * 60 * 60);
			} catch (UFex_Dao_NotFound $e) {
				$modified = null;
			}
			try {
				$ending = UFra::factory('UFbean_SruAdmin_PenaltyList');
				$ending->listEnding(24 * 60 * 60);
			} catch (UFex_Dao_NotFound $e) {
				$ending = null;
			}

			$body = $box->apiPenaltiesTimelineMailBody($added, $modified, $ending);
			$sender->sendMail('adnet@ds.pg.gda.pl', $title, $body, self::PREFIX);

			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
