<?
class IPSInformations extends IPSModule {

	public function Create() {
		//Never delete this line!
		parent::Create();
		//These lines are parsed on Symcon Startup or Instance creation
		//You cannot use variables here. Just static values.
		$this->RegisterPropertyInteger("UpdateIntervall", 60);
	}

	public function ApplyChanges() {
		//Never delete this line!
		parent::ApplyChanges();

		$this->RegisterVariableInteger("IPSEvents", "IPS Events");
		$this->RegisterVariableInteger("IPSInstanzen", "IPS Instanzen");
		$this->RegisterVariableInteger("IPSKategorien", "IPS Kategorien");
		$this->RegisterVariableInteger("IPSLinks", "IPS Links");
		$this->RegisterVariableInteger("IPSModule", "IPS Module");
		$this->RegisterVariableInteger("IPSObjekte", "IPS Objekte");
		$this->RegisterVariableInteger("IPSProfile", "IPS Profile");
		$this->RegisterVariableInteger("IPSSkripte", "IPS Skripte");
		$this->RegisterVariableInteger("IPSVariablen", "IPS Variablen");
		$this->Update();
	}
	
	public function Update() {
		$InstanzID = $this->InstanceID;
		$Timer_ID = @IPS_GetObjectIDByName("UpdateTimer", $InstanzID);
		$Update_Intervall_Min = $this->ReadPropertyInteger("UpdateIntervall");
		if ($Timer_ID === false) {
			$Timer_ID = IPS_CreateEvent(1);
			IPS_SetParent($Timer_ID, $InstanzID);
			IPS_SetName($Timer_ID, "UpdateTimer");
			IPS_SetEventCyclic($Timer_ID, 0, 0, 0, 0, 2, $Update_Intervall_Min);
			IPS_SetEventScript($Timer_ID, "IPSInfo_Update($InstanzID);");
			IPS_SetEventActive($Timer_ID, true);  
		}
		else {
			$Timer_Detail = IPS_GetEvent($Timer_ID);
			$Update_Intervall_Sek = $Timer_Detail['CyclicTimeValue'];
		  $Update_Intervall_MinX = $Update_Intervall_Sek / 60;
		  if ($Update_Intervall_MinX != $Update_Intervall_Min) {
				IPS_SetEventCyclic($Timer_ID, 0, 0, 0, 0, 2, $Update_Intervall_Min);
			}
		}
		
		//Anzahl IPS Events ermitteln
		$array = IPS_GetEventList();
		$count = count($array);
		if(GetValue($this->GetIDForIdent("IPSEvents")) != $count) {
			SetValueInteger($this->GetIDForIdent("IPSEvents"), $count);
		}
		$array = array();
		
		//Anzahl IPS Instanzen ermitteln
		$array = IPS_GetInstanceList();
		$count = count($array);
		if(GetValue($this->GetIDForIdent("IPSInstanzen")) != $count) {
			SetValueInteger($this->GetIDForIdent("IPSInstanzen"), $count);
		}
		$array = array();
		
		//Anzahl IPS Kategorien ermitteln
		$array = IPS_GetCategoryList();
		$count = count($array);
		if(GetValue($this->GetIDForIdent("IPSKategorien")) != $count) {
			SetValueInteger($this->GetIDForIdent("IPSKategorien"), $count);
		}
		$array = array();
		
		//Anzahl IPS Links ermitteln
		$array = IPS_GetLinkList();
		$count = count($array);
		if(GetValue($this->GetIDForIdent("IPSLinks")) != $count) {
			SetValueInteger($this->GetIDForIdent("IPSLinks"), $count);
		}
		$array = array();
		
		//Anzahl IPS Module ermitteln
		$array = IPS_GetModuleList();
		$count = count($array);
		if(GetValue($this->GetIDForIdent("IPSModule")) != $count) {
			SetValueInteger($this->GetIDForIdent("IPSModule"), $count);
		}
		$array = array();
		
		//Anzahl IPS Objekte ermitteln
		$array = IPS_GetObjectList();
		$count = count($array);
		if(GetValue($this->GetIDForIdent("IPSObjekte")) != $count) {
			SetValueInteger($this->GetIDForIdent("IPSObjekte"), $count);
		}
		$array = array();
		
		//Anzahl IPS Profile ermitteln
		$array = IPS_GetVariableProfileList();
		$count = count($array);
		if(GetValue($this->GetIDForIdent("IPSProfile")) != $count) {
			SetValueInteger($this->GetIDForIdent("IPSProfile"), $count);
		}
		$array = array();
		
		//Anzahl IPS Skripte ermitteln
		$array = IPS_GetScriptList();
		$count = count($array);
		if(GetValue($this->GetIDForIdent("IPSSkripte")) != $count) {
			SetValueInteger($this->GetIDForIdent("IPSSkripte"), $count);
		}
		$array = array();
		
		//Anzahl IPS Variablen ermitteln
		$array = IPS_GetVariableList();
		$count = count($array);
		if(GetValue($this->GetIDForIdent("IPSVariablen")) != $count) {
			SetValueInteger($this->GetIDForIdent("IPSVariablen"), $count);
		}
		$array = array();
	}
}
?>
