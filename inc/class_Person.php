<?php
class Person {
	public static $table_name = 'Person';
	
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
	public $sits_gnd_name;
	public $sits_frnm1;
	public $sits_known_as;
	public $sits_surnm;
	public $sits_pronouns;
	public $consolidated_pronouns;
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
	
	private ?LdapRecord\Models\ActiveDirectory\User $ldapRecordCache = null;
	
	public function __construct($lookup = null) {
		global $db;
	
		$result = null;
	
		if (is_array($lookup)) {
			foreach ($lookup as $field => $value) {
				if (!is_null($value)) {
					//echo "Trying lookup on $field for $value\n";
					$sql = "SELECT * FROM " . self::$table_name . " WHERE $field = :value LIMIT 1";
					$result = $db->get($sql, [':value' => $value], true);
	
					if ($result) {
						break; // Exit loop as soon as a match is found
					}
				}
			}
		} else {
			// Lookup by cudid directly
			$sql = "SELECT * FROM " . self::$table_name . " WHERE cudid = :value LIMIT 1";
			$result = $db->get($sql, [':value' => $lookup], true);
		}
	
		// Populate class properties if record found
		if ($result) {
			foreach ($result as $key => $value) {
				if (property_exists($this, $key)) {
					$this->$key = $value;
				}
			}
		}
	}
	
	public function isStudent() {
		$studentTypes = array("UG", "PG", "GT", "GR", "VS", "VD", "VV", "VR");
		
		if (in_array($this->university_card_type, $studentTypes)) {
			return true;
		}
		
		return false;
	}
	
	public function photograph() {
		$imgSrc = "images/person_photos/UAS_UniversityCard-" . $this->university_card_sysis . ".jpg";
	
		if (!file_exists($imgSrc)) {
			$imgSrc = "images/blank_avatar.png";
		}
	
		return $imgSrc;
	}
	
	public function card() {
		$personURL = "index.php?page=cud_person&cudid=" . $this->cudid;
		
		$output  = "<div class=\"col\">";
		$output .= "<div class=\"card\">";
		$output .= "<div class=\"ratio ratio-1x1\">";
		$output .= "<img src=\"" . $this->photograph() . "\" class=\"object-fit-cover card-img-top\" alt=\"...\">";
		$output .= "</div>";
		$output .= "<div class=\"card-body\">";
		$output .= "<h5 class=\"card-title\"><a href=\"" . $personURL . "\">" . $this->FullName . "</a></h5>";
		$output .= "<p class=\"card-text\">";
		$output .= $this->sso_username;
		$output .= $this->actionsButton();
		$output .= "</p>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
	
	public function actionsButton() {
		$output  = "<span class=\"ldap-status\" id=\"status-" . $this->cudid . "\"></span>";
		$output .= "<div class=\"btn-group\" role=\"group\">";
		$output .= "<div class=\"dropdown\">";
		$output .= "<button type=\"button\" class=\"btn btn-sm btn-outline-info dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">Actions</button>";
		
		$output .= "<ul class=\"dropdown-menu\">";
		
		$mailTo = $this->oxford_email;
		if (!empty($this->alt_email)) {
			$mailTo .= "?cc=" . $this->alt_email;
		}
		$output .= "<li><a class=\"dropdown-item\" href=\"mailto:" . $mailTo . "\">Email</a></li>";
		
		if (empty($this->getLDAPUsername())) {
			$output .= "<li><a class=\"dropdown-item ldap-provision-link\" data-cudid=\"" . $this->cudid . "\" data-action=\"disable\" href=\"#\">Provision User</a></li>";
		}
		
		if (in_array($this->ldapRecordCache['useraccountcontrol'][0], array('512','66048'))) {
			$output .= "<li><a class=\"dropdown-item ldap-toggle-link\" data-cudid=\"" . $this->cudid . "\" data-username=\"" . $this->getLDAPUsername() . "\" data-action=\"disable\" href=\"#\">Disable " . $this->sso_username . " LDAP Account</a></li>";
		} else {
			$output .= "<li><a class=\"dropdown-item ldap-toggle-link\" data-cudid=\"" . $this->cudid . "\" data-username=\"" . $this->getLDAPUsername() . "\" data-action=\"enable\" href=\"#\">Enable " . $this->sso_username . " LDAP Account</a></li>";
			$output .= "<li><a class=\"dropdown-item text-danger ldap-delete-link\" data-cudid=\"" . $this->cudid . "\" data-username=\"" . $this->getLDAPUsername() . "\" data-action=\"enable\" href=\"#\">Delete LDAP Account</a></li>";
		}
		
		$output .= "</ul>";
		$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
	
	public function getLdapRecord(): ?LdapRecord\Models\ActiveDirectory\User {
		global $ldap;
		
		if ($this->ldapRecordCache !== null) {
			return $this->ldapRecordCache;
		}
		// Key = LDAP attribute, Value = class property
		$lookups = array_filter([
			'samaccountname' => $this->sso_username ?? null,
			'pager'          => $this->MiFareID ?? null,
			'mail'           => $this->oxford_email ?? null,
		]);
		
		$ldapUser = $ldap->findUserFromLookups($lookups);
		
		if ($ldapUser) {
			$this->ldapRecordCache = $ldapUser;
			
			return $this->ldapRecordCache;
		}
	
		// Not found
		$this->ldapRecordCache = null;
		return null; 
	}
	
	public function getLDAPUsername(): string {
		$ldap = $this->getLdapRecord();
		
		if ($ldap && isset($ldap['samaccountname'][0])) {
			return $ldap['samaccountname'][0];
		}
		
		return false;
	}
	
	public function ssoButton() {
		if (!empty($this->cudid) && !empty($this->sso_username)) {
			$url = "index.php?page=cud_person&cudid=" . $this->cudid;
			
			return sprintf(
				'<a href="%s" class="btn btn-light position-relative">%s%s</a>',
				$url,
				htmlspecialchars($this->sso_username),
				$this->getSsoButtonBadge()
			);
		}
		
		return null;
	}
	
	private function getSsoButtonBadge(): string {
		$type = $this->university_card_type;
		
		if (in_array($type, array("UG"))) {
			$class = "bg-success";
		} elseif (in_array($type, array("PG", "GT", "GR"))) {
			$class = "bg-primary";
		} elseif (in_array($type, array("VS", "VD", "VV", "VR"))) {
			$class = "bg-dark";
		} else {
			$class = "bg-warning";
		}
		
		return sprintf(
			'<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill %s">%s</span>',
			$class,
			htmlspecialchars($type)
		);
	}
	
	public function getTypeBadge(): string {
		$type = $this->university_card_type;
		
		if (in_array($type, array("UG"))) {
			$class = "text-bg-success";
		} elseif (in_array($type, array("PG", "GT", "GR"))) {
			$class = "text-bg-primary";
		} elseif (in_array($type, array("VS", "VD", "VV", "VR"))) {
			$class = "text-bg-dark";
		} else {
			$class = "text-bg-warning";
		}
		
		return sprintf(
			'<span class="badge %s">%s</span>',
			$class,
			htmlspecialchars($type)
		);
	}
	
	public function age(): ?string {
		$ymd = $this->dob;
		
		if ($ymd === null) {
			return null;
		}
		
		if (!preg_match('/^\d{8}$/', $ymd)) {
			throw new InvalidArgumentException("Date must be in YYYYMMDD format.");
		}
		
		$birthdate = DateTime::createFromFormat('Ymd', $ymd);
		if (!$birthdate) {
			throw new InvalidArgumentException("Invalid date.");
		}
		
		$today = new DateTime();
		$diff = $today->diff($birthdate);
		
		if ($diff->y < 18) {
			return "{$diff->y} years and {$diff->m} months";
		} else {
			return "{$diff->y} years";
		}
	}
	
	public function addresses(): Addresses {
		return new Addresses($this->cudid);
	}
	
	public function applications(): Applications {
		return new Applications($this->cudid);
	}
	
	public function enrolments(): Enrolments {
		return new Enrolments($this->cudid);
	}
	
	public function collegefees(): CollegeFees {
		return new CollegeFees($this->cudid);
	}
	
	public function coowningdepartments(): CoOwningDepartments {
		return new CoOwningDepartments($this->cudid);
	}
	
	public function externalids(): ExternalIds {
		return new ExternalIds($this->cudid);
	}
	
	public function enrolawdprog(): EnrolAwdProg {
		return new EnrolAwdProg($this->cudid);
	}
	
	public function theresdeg(): TheResDeg {
		return new TheResDeg($this->cudid);
	}
	
	public function qualifications(): Qualifications {
		return new Qualifications($this->cudid);
	}
	
	public function supervisors(): Supervisors {
		return new Supervisors($this->cudid);
	}
	
	public function suspensions(): Suspensions {
		return new Suspensions($this->cudid);
	}
	
	public function yearsofawdprog(): YearsOfAwdProg {
		return new YearsOfAwdProg($this->cudid);
	}
}
