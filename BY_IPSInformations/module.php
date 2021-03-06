<?

class IPSInfo extends IPSModule
{

    public function Create()
    {
        //Never delete this line!
        parent::Create();
        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyInteger("Interval", 3600);
        $this->RegisterTimer("ReadSysInfo", 0, 'IPSInfo_Update($_IPS[\'TARGET\']);');
    }

    public function Destroy()
    {
        $this->UnregisterTimer("ReadSysInfo");
        parent::Destroy();
    }

    public function ApplyChanges()
    {
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
        $this->RegisterVariableInteger("IPSMedien", "IPS Medien");
        $this->RegisterVariableInteger("IPSLibrarys", "IPS Bibliotheken");

        $this->RegisterVariableFloat("IPSScriptDirSize", "IPS Scripte in MB");
        $this->RegisterVariableFloat("IPSLogDirSize", "IPS Logs in MB");
        $this->RegisterVariableFloat("IPSDBSize", "IPS Datenbank in MB");
        $this->RegisterVariableInteger("IPSStartTime", "Letzter IPS-Start", "~UnixTimestamp");


        $this->Update();
        $this->SetTimerInterval("ReadSysInfo", $this->ReadPropertyInteger("Interval"));
    }

    public function Update()
    {
        //Anzahl IPS Events ermitteln
        $this->SetValueInteger("IPSEvents", count(IPS_GetEventList()));

        //Anzahl IPS Instanzen ermitteln
        $this->SetValueInteger("IPSInstanzen", count(IPS_GetInstanceList()));

        //Anzahl IPS Kategorien ermitteln
        $this->SetValueInteger("IPSKategorien", count(IPS_GetCategoryList()));

        //Anzahl IPS Links ermitteln
        $this->SetValueInteger("IPSLinks", count(IPS_GetLinkList()));

        //Anzahl IPS Module ermitteln
        $this->SetValueInteger("IPSModule", count(IPS_GetModuleList()));

        //Anzahl IPS Objekte ermitteln
        $this->SetValueInteger("IPSObjekte", count(IPS_GetObjectList()));

        //Anzahl IPS Profile ermitteln
        $this->SetValueInteger("IPSProfile", count(IPS_GetVariableProfileList()));

        //Anzahl IPS Skripte ermitteln
        $this->SetValueInteger("IPSSkripte", count(IPS_GetScriptList()));

        //Anzahl IPS Variablen ermitteln
        $this->SetValueInteger("IPSVariablen", count(IPS_GetVariableList()));

        //Anzahl IPS Variablen ermitteln
        $this->SetValueInteger("IPSMedien", count(IPS_GetMediaList()));

        //Anzahl IPS Librarys ermitteln
        $this->SetValueInteger("IPSLibrarys", count(IPS_GetLibraryList()));

        // Groesse des Script-Verzeichnis
        $this->SetValueFloat("IPSScriptDirSize", $this->GetDirSize(IPS_GetKernelDir() . "scripts"));

        // Groesse des Log-Verzeichnis
        $this->SetValueFloat("IPSLogDirSize", $this->GetDirSize(IPS_GetLogDir()));

        // Groesse des Datenbank-Verzeichnis
        $this->SetValueFloat("IPSDBSize", $this->GetDirSize(IPS_GetKernelDir() . "db"));

        //Letzter IPS-Start
        $this->SetValueInteger("IPSStartTime", IPS_GetUptime());
    }

    private function GetDirSize($directory)
    {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file)
        {
            $size+=$file->getSize();
        }
        return round($size / 1024 / 1024, 2);
    }

    private function SetValueInteger($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueInteger($id) <> $value)
        {
            SetValueInteger($id, $value);
            return true;
        }
        return false;
    }

    private function SetValueFloat($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueFloat($id) <> $value)
        {
            SetValueFloat($id, $value);
            return true;
        }
        return false;
    }

    protected function RegisterTimer($Name, $Interval, $Script)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            $id = 0;


        if ($id > 0)
        {
            if (!IPS_EventExists($id))
                throw new Exception("Ident with name " . $Name . " is used for wrong object type", E_USER_WARNING);

            if (IPS_GetEvent($id)['EventType'] <> 1)
            {
                IPS_DeleteEvent($id);
                $id = 0;
            }
        }

        if ($id == 0)
        {
            $id = IPS_CreateEvent(1);
            IPS_SetParent($id, $this->InstanceID);
            IPS_SetIdent($id, $Name);
        }
        IPS_SetName($id, $Name);
        IPS_SetHidden($id, true);
        IPS_SetEventScript($id, $Script);
        if ($Interval > 0)
        {
            IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, $Interval);

            IPS_SetEventActive($id, true);
        } else
        {
            IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, 1);

            IPS_SetEventActive($id, false);
        }
    }

    protected function UnregisterTimer($Name)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id > 0)
        {
            if (!IPS_EventExists($id))
                throw new Exception('Timer not present', E_USER_NOTICE);
            IPS_DeleteEvent($id);
        }
    }

    protected function SetTimerInterval($Name, $Interval)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            throw new Exception('Timer not present', E_USER_WARNING);
        if (!IPS_EventExists($id))
            throw new Exception('Timer not present', E_USER_WARNING);

        $Event = IPS_GetEvent($id);

        if ($Interval < 1)
        {
            if ($Event['EventActive'])
                IPS_SetEventActive($id, false);
        }
        else
        {
            if ($Event['CyclicTimeValue'] <> $Interval)
                IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, $Interval);
            if (!$Event['EventActive'])
                IPS_SetEventActive($id, true);
        }
    }

}
?>
