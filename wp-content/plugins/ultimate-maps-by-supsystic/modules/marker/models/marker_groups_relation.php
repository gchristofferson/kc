<?php
class marker_groups_relationModelUms extends modelUms {
	function __construct() {
		$this->_setTbl('marker_groups_relation');
	}

	public function getRelationsByMarkerId($id){
//		$relations =  frameUms::_()->getTable('marker_groups_relation')->get('groups_id', 'marker_id = ' . $id, '', 'col');
		return frameUms::_()->getTable('marker_groups_relation')->get('groups_id', 'marker_id = ' . $id, '', 'col');
	}

}
