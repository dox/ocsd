<?php
class Persons {
	protected static $table_name = "Person";
	public $cudid;
	public $sits_student_code;
	public $oss_student_number;
	public $firstname;
	public $middlenames;
	public $lastname;
	public $FullName;
	public $known_as;
	public $prev_surnm;
	public $titl_cd;
	public $initials;
	public $gnd;
	public $dob;
	public $dom_cd;
	public $dom_hesa_cd;
	public $dom_name;
	public $birth_ctry_cd;
	public $birth_ctry_name;
	public $alt_email;
	public $oxford_email;
	public $sso_username;
	public $university_card_status;
	public $university_card_sysis;
	public $university_card_type;
	public $barcode;
	public $barcode7;
	public $University_Card_Start_Dt;
	public $University_Card_End_Dt;
	public $MiFareID;
	public $PaxonID;
	public $dept_cd;
	public $dept_desc;
	public $college_cd;
	public $co_owning_dept_code;
	public $div_cd;
	public $div_desc;
	public $rout_cd;
	public $rout_name;
	public $course_join_status;
	public $course_status;
	public $course_block;
	public $crs_start_dt;
	public $crs_exp_end_dt;
	public $crs_end_dt;
	public $mode_of_attendance;
	public $unit_set_cd;
	public $award_aim;
	public $universitycard_college;
	public $universitycard_type_expanded;
	public $universitycard_number_cards_issued;
	public $internal_tel;
	public $universitycard_isoiec_14443_uid;
	public $bodleian_mifare_id;
	public $bodleian_isoiec_14443_uid;
	
	function __construct() {
	}
	
	public function test() {
		return $this->cudid;
	}
	
	function allPersonsCount() {
		global $db;
		
		$persons = $db->get(self::$table_name);
		$personsCount = $db->count;
		
		return $personsCount;
	}
	
	public function all() {
		global $db;
		
		$persons = $db->orderBy('lastname', "ASC");
		$persons = $db->get(self::$table_name);
		
		return $persons;
	}
	
	public function search($searchTerm = null, $limit = null) {
		global $db;
		
		$persons = $db->orderBy('lastname', "ASC");
		$persons = $db->where('fullname', "%" . $searchTerm . "%", 'like');
		$persons = $db->orWhere('sso_username', "%" . $searchTerm . "%", 'like');
		$persons = $db->orWhere('barcode7', "%" . $searchTerm . "%", 'like');
		
		if (!$limit == null) {
			$persons = $db->get(self::$table_name, $limit);
		} else {
			$persons = $db->get(self::$table_name);
		}
		
		
		return $persons;
	}
} //end of class Persons
?>