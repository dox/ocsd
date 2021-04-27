<?php
class Person extends Persons {
	function __construct($cudid = null) {
		global $db;
		$sql = "SELECT * FROM " . self::$table_name . " WHERE cudid = '" . $cudid . "'";
		$person = $db->query($sql)->fetchArray();

		foreach ($person AS $key => $value) {
			$this->$key = $value;
		}

		$ldapPerson = new LDAPPerson($this->sso_username, $this->oxford_email);

		if (count($ldapPerson)) {
			$this->ldap_samaccountname = $ldapPerson->samaccountname;
			$this->ldap_isEnabled = $ldapPerson->isEnabled();
		}
	}

	public function isSuspended() {
		global $db;

		$sql  = "SELECT * FROM Enrolments";
		$sql .= " WHERE cudid = '" . $this->cudid . "'";
		$sql .= " AND Status = 'Suspended'";

		$currentSuspension = $db->query($sql)->fetchArray();

		if (!empty($currentSuspension['cudid'])) {
			return true;
		} else {
			return false;
		}
	}

	public function bodcardDaysLeft() {
		$now = time(); // or your date as well
		$your_date = strtotime($this->University_Card_End_Dt);
		$datediff = round(($your_date - $now) / (60 * 60 * 24));

		return $datediff;
	}

	public function makeListItem() {
		$cudURL = "./index.php?n=persons_unique&cudid=" . $this->cudid;
		$ldapURL = "./index.php?n=ldap_unique&samaccountname=" . $this->ldap_samaccountname;

		$output  = "<div class=\"card\">";
		$output .= "<div class=\"card-body\">";

		$output .= "<div class=\"row row-sm align-items-center\">";
		$output .= "<div class=\"col-auto\">";
		$output .= $this->avatar();
		$output .= "</div>";
		$output .= "<div class=\"col\">";

		$output .= "<div class=\"col-auto\">";
		$output .= "<div class=\"btn-group float-end\">";
		$output .= "<a href=\"" . $ldapURL . "\" class=\"btn btn-info btn-sm\">";
		$output .= "LDAP";
		$output .= "</a>";
		$output .= "<a href=\"" . $cudURL . "\" class=\"btn btn-primary btn-sm\">";
		$output .= "CUD Profile";
		$output .= "</a>";
		$output .= "</div>";
		$output .= "</div>";

		$output .= "<h3 class=\"mb-0 text-truncate\"><a href=\"" . $cudURL . "\">" . $this->FullName . "</a></h3>";

		$output .= "<div class=\"text-muted\">" . $this->bodcardType() . "</div>";
		$output .= "<div class=\"text-muted\">SSO:" . $this->sso_username . "</div>";
		$output .= "</div>";
		//$output .= "<div class=\"col-auto lh-1 align-self-start\">";
		//$output .= "<span class=\"badge bg-gray-lt\">";
		//$output .= $this->sso_username;
		//$output .= "</span>";
		//$output .= "</div>";
		$output .= "</div>";

		$output .= "<div class=\"row align-items-center mt-4\">";
		$output .= "<div class=\"col\">";
		$output .= "<div>";

		$datediff = $this->bodcardDaysLeft();
		if ($datediff > 365) {
			$width = "100%";
			$class = "bg-green";
		} elseif ($datediff <= 365 && $datediff > 100) {
			$width = $datediff . "%";
			$class = "bg-blue";
		} elseif ($datediff <= 100 && $datediff > 30) {
			$width = $datediff . "%";
			$class = "bg-yellow";
		} elseif ($datediff <= 30 && $datediff > 0) {
			$width = $datediff . "%";
			$class = "bg-red";
		} else {
			$width = "0%";
			$class = "bg-grey";
		}

		$output .= "<div class=\"d-flex mb-1 align-items-center lh-1\">";
		$output .= "<div class=\"\">" . $this->barcode7 . "</div> ";
		$output .= "<span class=\"ml-auto\"> " . $datediff . " days left</span>";
		$output .= "</div>";
		$output .= "</div>";

		$output .= "<div class=\"progress progress-sm\">";
		$output .= "<div class=\"progress-bar " . $class . "\" style=\"width: " . $width . "\" role=\"progressbar\" aria-valuenow=\"84\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>";
		$output .= "</div>";
		$output .= "</div>";




		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";

		//
		// NEW STYLE
		//
		/*
		$output  = "<div class=\"card\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<div class=\"row\">";

		$output .= "<div class=\"col-auto\">";
		$output .= $this->avatar();
		$output .= "</div>"; //end col-auto (avatar)

		$output .= "<div class=\"col\">";
		$output .= "<div class=\"text-truncate\">";
		$output .= "<strong>" . $this->FullName . "</strong> in service mode";
		$output .= "</div>"; //end text-truncate
		$output .= "<div class=\"text-muted\">yesterday</div>";
		$output .= "</div>"; // end col

		$output .= "<div class=\"col-auto align-self-center\">";
		$output .= "<div class=\"badge bg-primary\"></div>";
		$output .= "</div>"; //end col-auto align-self-center

		$output .= "</div>"; // end row
		$output .= "</div>"; // end body
		$output .= "</div>"; //end card
		*/
		return $output;
	}

	public function cardTypeBadge() {
		$cardType = $this->university_card_type;

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

	public function bodcardType() {
		$cardType = $this->university_card_type;
		$types = bodcardTypes();

		return $types[$cardType];
	}

	public function avatar() {
		$output  = "<a href=\"index.php?n=persons_unique&cudid=" . $this->cudid . "\">";


		//$output .= "<span alt=\"this is a test\" class=\"" . $class . "\" style=\"" . $style . "\">";
		$output .= "<img class=\"avatar rounded-2\" src=\"" . $this->photo() . "\" />";

		if ($this->ldap_isEnabled == true) {
			$class = "bg-success";
		} else {
			$class = "bg-danger";
		}

		//$output .= "<span class=\"badge " . $class . "\"></span>";

		$output .= "</a>";

		return $output;
	}

	public function photo() {
		$imgSrc = "photos/UAS_UniversityCard-" . $this->university_card_sysis . ".jpg";

		if (!file_exists($imgSrc)) {
			$imgSrc = "images/blank_avatar.png";
		}

		return $imgSrc;
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

	public function nationality() {
		global $db;

		$sql  = "SELECT * FROM Nationalities";
		$sql .= " WHERE cudid = '" . $this->cudid . "'";

		$nationality = $db->query($sql, 'test', 'test')->fetchArray();

		return $nationality['NatName'];
	}

	public function address($AddressTyp = "C") {
		global $db;

		$sql  = "SELECT * FROM Addresses";
		$sql .= " WHERE cudid = '" . $this->cudid . "'";
		$sql .= " AND AddressTyp = '" . $AddressTyp . "'";
		$sql .= " ORDER BY LastUpdateDt DESC";
		$sql .= " LIMIT 1";

		$addresses = $db->query($sql, 'test', 'test')->fetchArray();

		return $addresses;
	}

	public function addresses() {
		global $db;

		$sql  = "SELECT * FROM Addresses";
		$sql .= " WHERE cudid = '" . $this->cudid . "'";

		$addresses = $db->query($sql, 'test', 'test')->fetchAll();

		return $addresses;
	}

	public function contactDetails() {
		global $db;

		$sql  = "SELECT * FROM ContactDetails";
		$sql .= " WHERE cudid = '" . $this->cudid . "'";

		$contactDetails = $db->query($sql, 'test', 'test')->fetchAll();

		return $contactDetails;
	}

  public function getSuspensions() {
    global $db;

    $sql  = "SELECT * FROM Suspensions";
    $sql .= " WHERE cudid = '" . $this->cudid . "'";
    $sql .= " ORDER BY SuspendSeq DESC";

    $suspensions = $db->query($sql)->fetchAll();

    return $suspensions;
  }
} //end of class Person
?>
