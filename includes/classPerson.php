<?php
class Person extends Persons {
	function __construct($cudid = null) {
		global $db;

		$result = $db->where("cudid", $cudid);
		$result = $db->orWhere("sso_username", $cudid);
		$result = $db->getOne(self::$table_name);

		foreach ($result AS $key => $value) {
			$this->$key = $value;
		}
	}

	public function test() {
		return $this->cudid;
	}

	public function firstname() {
		$firstname = $this->firstname;

		if (isset($this->known_as) && $this->known_as != $this->firstname) {
			$firstname .= " (" . $this->known_as . ")";
		}
		return $firstname;
	}

	public function lastname() {
		$lastname = $this->lastname;

		if (isset($this->prev_surnm) && $this->prev_surnm != $this->lastname) {
			$lastname .= " (" . $this->prev_surnm . ")";
		}
		return $lastname;
	}

	public function fullName() {
		return $this->firstname() . " " . $this->middlenames . " " . $this->lastname();
	}

	public function photoCard() {
		$imgSrc = "../photos/UAS_UniversityCard-" . $this->university_card_sysis . ".jpg\"";

		$output  = "<div class=\"card float-right\" style=\"width: 18rem;\">";
		$output .= "<img src=\"" . $imgSrc . "\" class=\"card-img-top\" alt=\"...\">";
		$output .= "<div class=\"card-body\">";
		$output .= $this->bodcardBadge(true);
		$output .= "</div>";
		$output .= "</div>";

		return $output;
	}

	public function photoAvatar() {
		$imgSrc = "../photos/UAS_UniversityCard-" . $this->university_card_sysis . ".jpg\"";

		$output  = "<a href=\"index.php?n=persons_unique&cudid=" . $this->cudid . "\" class=\"circle\">";
		//$output  = "<img src=\"" . $imgSrc . "\" class=\"rounded-circle\" alt=\"...\">";
		$output .= "<img height=\"100\" width=\"100\" alt=\"100x100\" src=\"" . $imgSrc . "\">";
		$output .= "</a>";

		return $output;
	}

	public function bodcardBadge($displayText = false) {
		if (strtotime($this->University_Card_End_Dt) < strtotime("now")) {
			$bodcardCardBadeClass = "badge-danger";
			$bodcardCardText = "Expired: ";
		} else if (strtotime($this->University_Card_End_Dt) < strtotime("+30 days") && strtotime($this->University_Card_End_Dt) > strtotime("now")) {
			$bodcardCardBadeClass = "badge-warning";
			$bodcardCardText = "Expires Soon: " . date('Y-m-d', strtotime($this->University_Card_End_Dt));
		} else if (strtotime($this->University_Card_End_Dt) > strtotime("now")) {
			$bodcardCardBadeClass = "badge-success";
			$bodcardCardText = "Expires: " . date('Y-m-d', strtotime($this->University_Card_End_Dt));
		} else {
			$bodcardCardBadeClass = "badge-dark";
			$bodcardCardText = "Error: " . date('Y-m-d', strtotime($this->University_Card_End_Dt));
		}

		if ($displayText !== true) {
			$bodcardCardText = "";
		}

		$bodcardOutput  = "<span class=\"badge badge-pill " . $bodcardCardBadeClass . "\">";
		$bodcardOutput .=  $this->barcode7;

		if ($displayText == true) {
			$bodcardOutput .= " <span class=\"badge badge-pill badge-light\">" . $bodcardCardText . "</span>";
		}
		$bodcardOutput .= "</span>";

		return $bodcardOutput;
	}

	public function bodcardTypes() {
		$bodcardTypeArray = array(
			"MC" => "Congregation (from Register of Congregation)",
			"US" => "University Staff (on payroll)",
			"FS" => "Retiree (on University Pension) approved by a dept or college",
			"FR" => "Retiree (on University pension) approved by Pensions",
			"FB" => "Retiree (on University pension) approved by Pensions (no service entitlements)",
			"AV" => "Academic Visitor",
			"DS" => "Departmental Staff",
			"CS" => "College Staff",
			"GT" => "Postgraduate (from SITS)",
			"GR" => "Postgraduate (from SITS)",
			"UG" => "Undergraduate (from SITS)",
			"VR" => "Visiting/Recognized Student (from SITS)",
			"PT" => "Part Time (Continuing Education - unmatriculated)",
			"VD" => "Departmental Visiting Student (@dept.ox.ac.uk email address)",
			"VV" => "Departmental Visiting Student (@visiting.ox.ac.uk email address)",
			"VC (1)" => "College Visiting Student (@college.ox.ac.uk email address)",
			"CL" => "Cardholder (unit member, not a University member)",
			"CB" => "Cardholder (unit member, not a University member)",
			"VA" => "Virtual Access (neither unit nor University member)",
			"VX" => "Virtual Access (neither unit nor University member)",
			"leaver" => "Non-card status: leaver	Students in the 11 months after their University Card has expired (neither unit nor University member)"
		);

		return $bodcardTypeArray;
	}

	public function cardTypeBadge($cardType = null) {
		if ($cardType == null) {
			$cardType = $this->university_card_type;
		}
		if ($cardType == "GT" || $cardType == "GR" || $cardType == "PT") {
			$class = "badge-primary";
		} else if ($cardType == "UG" ) {
			$class = "badge-success";
		} else if ($cardType == "VR" || $cardType == "VD" || $cardType == "VV" || $cardType == "VC") {
			$class = "badge-warning";
		} else if ($cardType == "CS") {
			$class = "badge-info";
		} else {
			$class = "badge-secondary";
		}

		$output  = "<a href=\"index.php?n=card_types\" class=\"badge " . $class . "\">" . $cardType . "</a>";

		return $output;
	}

	public function tableRow () {
		$output  = "<tr>";
		$output .= "<td>" . $this->cardTypeBadge() . " </td>";
		$output .= "<td><a href=\"index.php?n=persons_unique&cudid=" . $this->cudid . "\">" . $this->firstname() . "</a></td>";
		$output .= "<td><a href=\"index.php?n=persons_unique&cudid=" . $this->cudid . "\">" . $this->lastname() . "</a></td>";
		$output .= "<td>" . $this->bodcardBadge() . "</td>";
		$output .= "<td><a href=\"index.php?n=persons_unique&cudid=" . $this->cudid . "\">" . $this->sso_username . "</a></td>";
		$output .= "</tr>";

		return $output;
	}
} //end of class Person
?>
