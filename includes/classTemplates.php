<?php
class Templates  {
  protected static $table_name = "_templates";

	public $cudid;
	public $sits_student_code;
	public $oss_student_number;

  public function all() {
		global $db;

		$sql = "SELECT * FROM " . self::$table_name;
		$templates = $db->query($sql)->fetchAll();

		return $templates;
	}

  public function one($uid = null) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $uid . "'";
    $sql .= " OR name = '" . $uid . "'";

		$template = $db->query($sql)->fetchArray();

		return $template;
	}

  public function oneBodyWithReplaceFields($name = null, $replaceFields = null) {
    $template = $this->one($name);

    if ($template) {
      $messageBody = $template['body'];

      foreach ($replaceFields AS $key => $value) {
        $messageBody = str_replace("{{" . $key . "}}", $value, $messageBody);
      }
    }

    return $messageBody;
  }
}
?>
