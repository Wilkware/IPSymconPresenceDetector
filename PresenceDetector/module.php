<?php

declare(strict_types=1);

// General functions
require_once __DIR__ . '/../libs/_traits.php';

// CLASS PresenceDetector
class PresenceDetector extends IPSModule
{
    use DebugHelper;
    use EventHelper;
    use ProfileHelper;

    // Schedule constant
    const SCHEDULE_NAME = 'Zeitplan';
    const SCHEDULE_IDENT = 'circuit_diagram';
    const SCHEDULE_SWITCH = [
        1 => ['Aktiv', 0x00FF00, ''],
        2 => ['Inaktiv', 0xFF0000, ''],
    ];

    /**
     * Create.
     */
    public function Create()
    {
        //Never delete this line!
        parent::Create();
        $this->RegisterPropertyInteger('MotionVariable', 0);
        $this->RegisterPropertyInteger('BrightnessVariable', 0);
        $this->RegisterPropertyInteger('ThresholdValue', 0);
        $this->RegisterPropertyInteger('SwitchVariable', 0);
        $this->RegisterPropertyInteger('EventVariable', 0);
        $this->RegisterPropertyInteger('ScriptVariable', 0);
        $this->RegisterPropertyBoolean('OnlyBool', false);
        $this->RegisterPropertyBoolean('ThresholdVariable', false);
    }

    /**
     * Apply Configuration Changes.
     */
    public function ApplyChanges()
    {
        if ($this->ReadPropertyInteger('MotionVariable') != 0) {
            $this->UnregisterMessage($this->ReadPropertyInteger('MotionVariable'), VM_UPDATE);
        }
        //Never delete this line!
        parent::ApplyChanges();
        // Profiles
        $association = [
            [0, 'Always', '', 0xFFFF00],
            [5, '5 lx', '', 0xFFFF00],
            [10, '10 lx', '', 0xFFFF00],
            [15, '15 lx', '', 0xFFFF00],
            [20, '20 lx', '', 0xFFFF00],
            [25, '25 lx', '', 0xFFFF00],
            [30, '30 lx', '', 0xFFFF00],
            [35, '35 lx', '', 0xFFFF00],
            [40, '40 lx', '', 0xFFFF00],
            [45, '45 lx', '', 0xFFFF00],
            [50, '50 lx', '', 0xFFFF00],
            [75, '75 lx', '', 0xFFFF00],
            [100, '100 lx', '', 0xFFFF00],
        ];
        $this->RegisterProfile(vtInteger, 'TPD.Threshold', 'Light', '', '', 0, 0, 0, 0, $association);
        // Threshold
        $threshold = $this->ReadPropertyBoolean('ThresholdVariable');
        $this->MaintainVariable('BrightnessThreshold', $this->Translate('Brightness threshold'), vtInteger, 'TPD.Threshold', 2, $threshold);
        if ($threshold) {
            $this->EnableAction('BrightnessThreshold');
        }
        //Create our trigger
        if (IPS_VariableExists($this->ReadPropertyInteger('MotionVariable'))) {
            $this->RegisterMessage($this->ReadPropertyInteger('MotionVariable'), VM_UPDATE);
        }
    }

    /**
     * MessageSink
     * data[0] = new Value
     * data[1] = value changed?
     * data[2] = old value
     * data[3] = timestamp.
     */
    public function MessageSink($timeStamp, $senderID, $message, $data)
    {
        switch ($message) {
            case VM_UPDATE:
                // Safety Check
                if ($senderID != $this->ReadPropertyInteger('MotionVariable')) {
                    $this->SendDebug(__FUNCTION__, $senderID . ' unknown!');
                    break;
                }
                // Wochenprogramm auswerten!
                $eid = $this->ReadPropertyInteger('EventVariable');
                if ($eid != 0) {
                    $state = $this->GetWeeklyScheduleInfo($eid);
                    if ($state['WeekPlanActiv'] == 1 && $state['ActionID'] == 2) {
                        $this->SendDebug(__FUNCTION__, 'Schedule plan is inactiv!');
                        break;
                    }
                }
                // OnChange auf TRUE, d.h. Bewegung erkannt
                if ($data[0] == true && $data[1] == true) {
                    $this->SendDebug(__FUNCTION__, 'OnChange on TRUE - motion detected');
                    $this->SwitchState();
                } elseif ($data[0] == false && $data[1] == true) { // OnChange auf FALSE, d.h. keine Bewegung
                    $this->SendDebug(__FUNCTION__, 'OnChange on FALSE - no motion');
                } else { // OnChange auf FALSE, d.h. keine Bewegung
                    $this->SendDebug(__FUNCTION__, 'OnChange unchanged - status not changed');
                }
            break;
        }
    }

    /**
     * RequestAction.
     *
     *  @param string $ident Ident.
     *  @param string $value Value.
     */
    public function RequestAction($ident, $value)
    {
        // Debug output
        $this->SendDebug(__FUNCTION__, $ident . ' => ' . $value);
        // Ident == OnXxxxxYyyyy
        switch ($ident) {
            case 'BrightnessThreshold':
                $this->SetValueInteger($ident, $value);
            break;
        }
        //return true;
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
            if ($this->ReadPropertyBoolean('ThresholdVariable')) {
                $tv = $this->GetValue('BrightnessThreshold');
            }
            if ($tv != 0 && $bv > $tv) {
                $this->SendDebug(__FUNCTION__, 'Above threshold: ' . $bv . '(Threshold: ' . $tv . ')');
                return; // nothing to do
            }
            $this->SendDebug(__FUNCTION__, 'Always or below threshold: ' . $bv . ' (Threshold: ' . $tv . ')');
        }
        // Switch variable
        if ($this->ReadPropertyInteger('SwitchVariable') != 0) {
            $sv = $this->ReadPropertyInteger('SwitchVariable');
            if ($this->ReadPropertyBoolean('OnlyBool') == true) {
                SetValueBoolean($sv, true);
            } else {
                $ret = @RequestAction($sv, true); // switch on device
                if ($ret === false) {
                    $this->SendDebug(__FUNCTION__, 'Device could not be switched on (UNREACH)!');
                }
            }
            $this->SendDebug(__FUNCTION__, 'Variable (#' . $sv . ') on TRUE switched!');
        }
        // Run script
        if ($this->ReadPropertyInteger('ScriptVariable') != 0) {
            if (IPS_ScriptExists($this->ReadPropertyInteger('ScriptVariable'))) {
                $mID = $this->ReadPropertyInteger('MotionVariable');
                $bID = $this->ReadPropertyInteger('BrightnessVariable');
                $sID = $this->ReadPropertyInteger('SwitchVariable');
                $tVA = $this->ReadPropertyInteger('ThresholdValue');
                if ($this->ReadPropertyBoolean('ThresholdVariable')) {
                    $tVA = $this->GetValue('BrightnessThreshold');
                }
                $ret = IPS_RunScriptEx(
                    $this->ReadPropertyInteger('ScriptVariable'),
                    ['MotionVariable' => $mID, 'BrightnessVariable' => $bID, 'SwitchVariable' => $sID, 'ThresholdValue' => $tVA]
                );
                $this->SendDebug(__FUNCTION__, 'Script return value: ' . $ret);
            }
        }
    }

    /**
     * Creates a schedule plan.
     */
    public function CreateSchedule()
    {
        $eid = $this->CreateWeeklySchedule($this->InstanceID, self::SCHEDULE_NAME, self::SCHEDULE_IDENT, self::SCHEDULE_SWITCH, -1);
        if ($eid !== false) {
            $this->UpdateFormField('EventVariable', 'value', $eid);
        }
    }

    /**
     * Update a boolean value.
     *
     * @param string $ident Ident of the boolean variable
     * @param bool   $value Value of the boolean variable
     */
    private function SetValueBoolean(string $ident, bool $value)
    {
        $id = $this->GetIDForIdent($ident);
        SetValueBoolean($id, $value);
    }

    /**
     * Update a string value.
     *
     * @param string $ident Ident of the string variable
     * @param string $value Value of the string variable
     */
    private function SetValueString(string $ident, string $value)
    {
        $id = $this->GetIDForIdent($ident);
        SetValueString($id, $value);
    }

    /**
     * Update a integer value.
     *
     * @param string $ident Ident of the integer variable
     * @param int    $value Value of the integer variable
     */
    private function SetValueInteger(string $ident, int $value)
    {
        $id = $this->GetIDForIdent($ident);
        SetValueInteger($id, $value);
    }
}
