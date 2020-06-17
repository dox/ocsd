<?php
class Person extends Persons {
	function __construct($cudid = null) {
		global $db;

		$result = $db->where("cudid", $cudid);
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
	public function cardTypeBadge() {
		if ($this->university_card_type == "GT" || $this->university_card_type == "GR" || $this->university_card_type == "PT") {
			$class = "primary";
		} else if ($this->university_card_type == "UG" ) {
			$class = "success";
		} else if ($this->university_card_type == "VR" || $this->university_card_type == "VD" || $this->university_card_type == "VV" || $this->university_card_type == "VC") {
			$class = "warning";
		} else if ($this->university_card_type == "CS") {
			$class = "info";
		} else {
			$class = "secondary";
		}
		$output  = "<span class=\"badge badge-" . $class . "\">";
		$output .= $this->university_card_type;
		$output .= "</span>";

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
