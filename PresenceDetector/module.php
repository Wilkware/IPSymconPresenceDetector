<?php

declare(strict_types=1);

// Allgemeine Funktionen
require_once __DIR__ . '/../libs/traits.php';

// CLASS PresenceDetector
class PresenceDetector extends IPSModule
{
    use DebugHelper;

    public function Create()
    {
        //Never delete this line!
        parent::Create();
        $this->RegisterPropertyInteger('MotionVariable', 0);
        $this->RegisterPropertyInteger('BrightnessVariable', 0);
        $this->RegisterPropertyInteger('ThresholdValue', 0);
        $this->RegisterPropertyInteger('SwitchVariable', 0);
        $this->RegisterPropertyInteger('ScriptVariable', 0);
        $this->RegisterPropertyBoolean('OnlyBool', false);
    }

    public function ApplyChanges()
    {
        if ($this->ReadPropertyInteger('MotionVariable') != 0) {
            $this->UnregisterMessage($this->ReadPropertyInteger('MotionVariable'), VM_UPDATE);
        }
        //Never delete this line!
        parent::ApplyChanges();
        //Create our trigger
        if (IPS_VariableExists($this->ReadPropertyInteger('MotionVariable'))) {
            $this->RegisterMessage($this->ReadPropertyInteger('MotionVariable'), VM_UPDATE);
        }
    }

    /**
     * Interne Funktion des SDK.
     * data[0] = neuer Wert
     * data[1] = wurde Wert geändert?
     * data[2] = alter Wert
     * data[3] = Timestamp.
     */
    public function MessageSink($timeStamp, $senderID, $message, $data)
    {
        // $this->SendDebug('MessageSink', 'SenderId: '. $senderID . 'Data: ' . print_r($data, true), 0);
        switch ($message) {
            case VM_UPDATE:
                if ($senderID != $this->ReadPropertyInteger('MotionVariable')) {
                    // Safety Check
                    $this->SendDebug('MessageSink', $senderID . ' unbekannt!');
                    break;
                }
                if ($data[0] == true && $data[1] == true) { // OnChange auf TRUE, d.h. Bewegung erkannt
                    $this->SendDebug('MessageSink', 'OnChange auf TRUE - Bewegung erkannt');
                    $this->SwitchState();
                } elseif ($data[0] == false && $data[1] == true) { // OnChange auf FALSE, d.h. keine Bewegung
                    $this->SendDebug('MessageSink', 'OnChange auf FALSE - keine Bewegung');
                } else { // OnChange auf FALSE, d.h. keine Bewegung
                    $this->SendDebug('MessageSink', 'OnChange unverändert - keine Zustandsänderung');
                }
            break;
        }
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * TPD_SwitchState($id);
     */
    public function SwitchState()
    {
        // Check Brightness
        if ($this->ReadPropertyInteger('BrightnessVariable') != 0) {
            $bv = GetValue($this->ReadPropertyInteger('BrightnessVariable'));
            $tv = $this->ReadPropertyInteger('ThresholdValue');
            if ($tv != 0 && $bv > $tv) {
                $this->SendDebug('SwitchState', 'Oberhalb Schwellwert: ' . $bv . '(Schwellwert: ' . $tv . ')');
                return; // nix zu tun
            }
            $this->SendDebug('SwitchState', 'Immer oder unterhalb Schwellwert: ' . $bv . ' (Schwellwert: ' . $tv . ')');
        }
        // Variable schalten
        if ($this->ReadPropertyInteger('SwitchVariable') != 0) {
            $sv = $this->ReadPropertyInteger('SwitchVariable');
            if ($this->ReadPropertyBoolean('OnlyBool') == true) {
                SetValueBoolean($sv, true);
            } else {
                //$pid = IPS_GetParent($sv);
                //$ret = @HM_WriteValueBoolean($pid, 'STATE', true); //Ger�t einschalten
                $ret = @RequestAction($sv, true); //Gerät einschalten
                if ($ret === false) {
                    $this->SendDebug('SwitchState', 'Gerät konnte nicht eingeschalten werden (UNREACH)!');
                }
            }
            $this->SendDebug('SwitchState', 'Variable (#' . $sv . ') auf true geschalten!');
        }
        // Script ausführen
        if ($this->ReadPropertyInteger('ScriptVariable') != 0) {
            if (IPS_ScriptExists($this->ReadPropertyInteger('ScriptVariable'))) {
                $mID = $this->ReadPropertyInteger('MotionVariable');
                $bID = $this->ReadPropertyInteger('BrightnessVariable');
                $sID = $this->ReadPropertyInteger('SwitchVariable');
                $tVA = $this->ReadPropertyInteger('ThresholdValue');
                $ret = IPS_RunScriptEx(
                    $this->ReadPropertyInteger('ScriptVariable'),
                    ['MotionVariable' => $mID, 'BrightnessVariable' => $bID, 'SwitchVariable' => $sID, 'ThresholdValue' => $tVA]
                );
                $this->SendDebug('SwitchState', 'Script Return Value: ' . $ret);
            }
        }
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * TPD_SetThreshold($id, $threshold);
     *
     * @param bool $threshold Helligkeitsschwellwert ab welchem geschalten werden soll.
     * @return bool true if successful, otherwise false.
     */
    public function SetThreshold(int $threshold)
    {
        if ((($threshold % 5) == 0) && $threshold >= 0 && $threshold <= 50 || $threshold = 75 || $threshold = 100) {
            IPS_SetProperty($this->InstanceID, 'ThresholdValue', $threshold);
            IPS_ApplyChanges($this->InstanceID);
            return true;
        }
        return false;
    }
}
