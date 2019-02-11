<?php
class installerDbUpdaterUms {
	static public function runUpdate() {
		self::update_105();
		self::update_109();
		self::update_117();
		self::update_192();
	}
	public static function update_105() {
		if(!dbUms::exist('@__modules', 'code', 'csv')) {
			dbUms::query("INSERT INTO `@__modules` (id, code, active, type_id, params, has_tab, label, description)
				VALUES (NULL, 'csv', 1, 1, '', 0, 'csv', 'csv')");
		}
	}
	public static function update_109() {
		if(!dbUms::exist('@__modules', 'code', 'maps_widget')) {
			dbUms::query("INSERT INTO `@__modules` (id, code, active, type_id, params, has_tab, label, description)
				VALUES (NULL, 'maps_widget', 1, 1, '', 0, 'maps_widget', 'maps_widget')");
		}
	}
	public static function update_117() {
		dbUms::query("UPDATE @__options SET value_type = 'array' WHERE code = 'infowindow_size' LIMIT 1");
	}

	public static function update_192() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix;

		if(!dbUms::exist($wpPrefix.UMS_DB_PREF."markers", 'period_from')) {
			dbUms::query("ALTER TABLE `@__markers` ADD COLUMN `period_from` DATE NULL;");
		}
		if(!dbUms::exist($wpPrefix.UMS_DB_PREF."markers", 'period_to')) {
			dbUms::query("ALTER TABLE `@__markers` ADD COLUMN `period_to` DATE NULL;");
		}
		if(!dbUms::exist($wpPrefix.UMS_DB_PREF."markers", 'hash')) {
			dbUms::query("ALTER TABLE `@__markers` ADD COLUMN `hash` varchar(32) DEFAULT NULL;");
		}
	}
}