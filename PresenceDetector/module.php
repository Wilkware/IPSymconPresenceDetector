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
    use VariableHelper;

    // Schedule constant
    public const SCHEDULE_NAME = 'Zeitplan';
    public const SCHEDULE_IDENT = 'circuit_diagram';
    public const SCHEDULE_SWITCH = [
        1 => ['Aktiv', 0x00FF00, ''],
        2 => ['Inaktiv', 0xFF0000, ''],
    ];

    // Devices constant
    private const DEVICE_ONE = 0;
    private const DEVICE_MULTIPLE = 1;

    // Logical constant
    private const LINK_AND = 0;
    private const LINK_OR = 1;
    private const LINK_NOT = 2;

    // Sensor delay time frame (milliseconds)
    private const DELAY_TIME = 500;

    /**
     * Create.
     */
    public function Create()
    {
        //Never delete this line!
        parent::Create();
        // Sensors
        $this->RegisterPropertyInteger('LogicalLink', self::LINK_OR);
        $this->RegisterPropertyInteger('MotionVariable', 0);
        $this->RegisterPropertyInteger('SensorVariable', 0);
        $this->RegisterPropertyInteger('SensorDelay', self::DELAY_TIME);
        $this->RegisterPropertyInteger('BrightnessVariable', 0);
        $this->RegisterPropertyInteger('ThresholdValue', 0);

        //Schedule
        $this->RegisterPropertyInteger('EventVariable', 0);
        // Device
        $this->RegisterPropertyInteger('DeviceNumber', 0);
        $this->RegisterPropertyInteger('SwitchVariable', 0);
        $this->RegisterPropertyString('SwitchVariables', '[]');
        $this->RegisterPropertyInteger('ScriptVariable', 0);
        // Settings
        $this->RegisterPropertyBoolean('ThresholdVariable', false);
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
            [150, '150 lx', '', 0xFFFF00],
            [200, '200 lx', '', 0xFFFF00],
            [250, '250 lx', '', 0xFFFF00],
            [500, '500 lx', '', 0xFFFF00],
        ];
        $this->RegisterProfile(vtInteger, 'TPD.Threshold', 'Light', '', '', 0, 0, 0, 0, $association);
        // Attribute
        $this->RegisterAttributeInteger('Trigger', 0);
        // Timer
        $this->RegisterTimer('TPD.Timer', 0, 'IPS_RequestAction(' . $this->InstanceID . ', "DelayTrigger", "");');
    }

    /**
     * Destroy.
     */
    public function Destroy()
    {
        parent::Destroy();
    }

    /**
     * Configuration Form.
     *
     * @return JSON configuration string.
     */
    public function GetConfigurationForm()
    {
        // Get Form
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        // number of devices
        $number = $this->ReadPropertyInteger('DeviceNumber');
        $form['elements'][4]['items'][1]['visible'] = ($number === self::DEVICE_ONE);
        $form['elements'][4]['items'][2]['visible'] = ($number === self::DEVICE_MULTIPLE);
        // device list (set status column)
        $variables = json_decode($this->ReadPropertyString('SwitchVariables'), true);
        foreach ($variables as $variable) {
            $form['elements'][4]['items'][2]['values'][] = [
                'Status' => $this->GetVariableStatus($variable['VariableID']),
            ];
        }
        // return form
        return json_encode($form);
    }

    /**
     * Apply Configuration Changes.
     */
    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        //Delete all references in order to readd them
        foreach ($this->GetReferenceList() as $referenceID) {
            $this->UnregisterReference($referenceID);
        }

        //Delete all registrations in order to readd them
        foreach ($this->GetMessageList() as $senderID => $messages) {
            foreach ($messages as $message) {
                $this->UnregisterMessage($senderID, $message);
            }
        }
        //Register references
        $variable = $this->ReadPropertyInteger('MotionVariable');
        if (IPS_VariableExists($variable)) {
            $this->RegisterReference($variable);
        }
        $variable = $this->ReadPropertyInteger('SensorVariable');
        if (IPS_VariableExists($variable)) {
            $this->RegisterReference($variable);
        }
        $variable = $this->ReadPropertyInteger('BrightnessVariable');
        if (IPS_VariableExists($variable)) {
            $this->RegisterReference($variable);
        }
        $event = $this->ReadPropertyInteger('EventVariable');
        if (IPS_EventExists($event)) {
            $this->RegisterReference($event);
        }
        $script = $this->ReadPropertyInteger('ScriptVariable');
        if (IPS_ScriptExists($script)) {
            $this->RegisterReference($script);
        }
        $variables = json_decode($this->ReadPropertyString('SwitchVariables'), true);
        foreach ($variables as $variable) {
            if (IPS_VariableExists($variable['VariableID'])) {
                $this->RegisterReference($variable['VariableID']);
            }
        }
        $variable = $this->ReadPropertyInteger('SwitchVariable');
        if (IPS_VariableExists($variable)) {
            $this->RegisterReference($variable);
        }

        //Safty check
        $variable = $this->ReadPropertyInteger('MotionVariable');
        if (($variable > 0) && !IPS_VariableExists($variable)) {
            $this->SendDebug(__FUNCTION__, 'MotionVariable: ' . $variable);
            $this->SetStatus(104);
            return;
        }
        $variable = $this->ReadPropertyInteger('SensorVariable');
        if (($variable > 0) && !IPS_VariableExists($variable)) {
            $this->SendDebug(__FUNCTION__, 'SensorVariable: ' . $variable);
            $this->SetStatus(104);
            return;
        }
        $variable = $this->ReadPropertyInteger('BrightnessVariable');
        if (($variable > 0) && !IPS_VariableExists($variable)) {
            $this->SendDebug(__FUNCTION__, 'BrightnessVariable: ' . $variable);
            $this->SetStatus(104);
            return;
        }
        $event = $this->ReadPropertyInteger('EventVariable');
        if (($event > 0) && !IPS_EventExists($event)) {
            $this->SendDebug(__FUNCTION__, 'EventVariable: ' . $event);
            $this->SetStatus(104);
            return;
        }
        $script = $this->ReadPropertyInteger('ScriptVariable');
        if (($script > 0) && !IPS_ScriptExists($script)) {
            $this->SendDebug(__FUNCTION__, 'ScriptVariable: ' . $script);
            $this->SetStatus(104);
            return;
        }

        $number = $this->ReadPropertyInteger('DeviceNumber');
        if ($number == self::DEVICE_ONE) {
            $variable = $this->ReadPropertyInteger('SwitchVariable');
            if (!IPS_VariableExists($variable)) {
                $this->SendDebug(__FUNCTION__, 'SwitchVariable: ' . $variable);
                $this->SetStatus(104);
                return;
            }
        } else {
            $ok = 0;
            foreach ($variables as $variable) {
                if ($this->GetVariableStatus($variable['VariableID']) == 'OK') {
                    $ok++;
                }
            }
            if ((empty($variables)) || ($ok != count($variables))) {
                $this->SendDebug(__FUNCTION__, 'SwitchVariables: ' . $ok);
                $this->SetStatus(104);
                return;
            }
        }

        //Register update messages = Create our trigger
        if (IPS_VariableExists($this->ReadPropertyInteger('MotionVariable'))) {
            $this->RegisterMessage($this->ReadPropertyInteger('MotionVariable'), VM_UPDATE);
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('SensorVariable'))) {
            $this->RegisterMessage($this->ReadPropertyInteger('SensorVariable'), VM_UPDATE);
        }

        // Threshold
        $threshold = $this->ReadPropertyBoolean('ThresholdVariable');
        $this->MaintainVariable('BrightnessThreshold', $this->Translate('Brightness threshold'), vtInteger, 'TPD.Threshold', 2, $threshold);
        if ($threshold) {
            $this->EnableAction('BrightnessThreshold');
        }
        $this->SetStatus(102);
    }

    /**
     * MessageSink
     * data[0] = new Value
     * data[1] = value changed?
     * data[2] = old value
     * data[3] = timestamp.
     */
    public function MessageSink($timestamp, $sender, $message, $data)
    {
        switch ($message) {
            case VM_UPDATE:
                // Safety Check
                $id1 = $this->ReadPropertyInteger('MotionVariable');
                $id2 = $this->ReadPropertyInteger('SensorVariable');
                if (($sender != $id1) && ($sender != $id2)) {
                    $this->SendDebug(__FUNCTION__, $sender . ' unknown!');
                    break;
                }
                // OnChange on TRUE, i.e. motion detected
                if ($data[0] == true && $data[1] == true) {
                    $this->SendDebug(__FUNCTION__, 'OnChange on TRUE - motion detected ' . $sender);
                    $this->ProcessData($sender, $id1, $id2);
                } elseif ($data[0] == false && $data[1] == true) { // OnChange on FALSE, i.e. no motion
                    $this->SendDebug(__FUNCTION__, 'OnChange on FALSE - no motion');
                } else { // OnChange on FALSE, i.e. no change of status
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
            case 'DelayTrigger':
                $this->ProcessData();
                break;
            default:
                eval('$this->' . $ident . '(\'' . $value . '\');');
        }
        //return true;
    }

    /**
     * Main entry point for the whole switch process.
     *
     * @param int $sender Sender ID
     * @param int $id1 Motion sensor ID 1
     * @param int $id2 Motion sensor ID 2
     */
    private function ProcessData($sender = 0, $id1 = -1, $id2 = -1)
    {
        // first step is to check logical link
        if (!$this->CheckLink($sender, $id1, $id2)) {
            return;
        }
        // next step is to check weekly schedule
        if (!$this->CheckSchedule()) {
            return;
        }
        // next step is to check brightness
        if (!$this->CheckBrightness()) {
            return;
        }
        // next step is to switch devices
        $this->SwitchDevices();
        // last step is the script execution
        $this->ExecuteScript();
    }

    /**
     * Check logical link
     *
     * @param int $sender Sender ID
     * @param int $id1 Motion sensor ID 1
     * @param int $id2 Motion sensor ID 2
     */
    private function CheckLink($sender, $id1, $id2)
    {
        // Exist logical link
        if (($id1 == 0) || ($id2 == 0)) {
            $this->SendDebug(__FUNCTION__, 'no link - no check: ' . $id1 . ' : ' . $id2);
            return true; // no link no check
        }
        $this->SendDebug(__FUNCTION__, 'Sender: ' . $sender);
        // Check locical link
        $link = $this->ReadPropertyInteger('LogicalLink');
        $time = $this->ReadPropertyInteger('SensorDelay');
        switch ($link) {
            case 0: // AND
                // Timer expired ?
                if ($sender == 0) {
                    break;
                }
                // check pre condition
                $trigger = $this->ReadAttributeInteger('Trigger');
                $this->SendDebug(__FUNCTION__, 'Trigger (and): ' . $trigger);
                if (($trigger != 0) && ($trigger != $sender)) {
                    $this->SendDebug(__FUNCTION__, 'AND condition fulfilled!');
                    // AND condition fulfilled => reset all => fire
                    $this->SetTimerInterval('TPD.Timer', 0);
                    $this->WriteAttributeInteger('Trigger', 0);
                    return true;
                }
                if ($trigger == 0) {
                    $this->SendDebug(__FUNCTION__, 'AND condition not fulfilled');
                    // AND condition not fulfilled => init all => wait
                    if ($time > 0) {
                        $this->SetTimerInterval('TPD.Timer', $time);
                        $this->WriteAttributeInteger('Trigger', $sender);
                    }
                    return false; // we wait
                }
                break;
            case 1: // OR
                // Timer expired ?
                if ($sender == 0) {
                    break;
                }
                // check pre condition
                $trigger = $this->ReadAttributeInteger('Trigger');
                $this->SendDebug(__FUNCTION__, 'Trigger (or): ' . $trigger);
                if ($trigger == 0) {
                    $this->SendDebug(__FUNCTION__, 'OR condition ');
                    // OR condition fulfilled => fire and wait to block reswitch
                    if ($time > 0) {
                        $this->SetTimerInterval('TPD.Timer', $time);
                        $this->WriteAttributeInteger('Trigger', $sender);
                    }
                    return true; // and wait
                }
                break;
            case 2: // NOT
                $trigger = $this->ReadAttributeInteger('Trigger');
                $this->SendDebug(__FUNCTION__, 'Trigger (not): ' . $trigger);
                if (($sender == 0) && ($trigger > 0)) {
                    $this->SendDebug(__FUNCTION__, 'NOT condition fulfilled!');
                    // NOR condition fulfilled => reset all => fire
                    $this->SetTimerInterval('TPD.Timer', 0);
                    $this->WriteAttributeInteger('Trigger', 0);
                    return true;
                }
                if (($sender != 0) && ($trigger == 0)) {
                    $this->SendDebug(__FUNCTION__, 'NOR condition still fulfilled (wait)');
                    // NOR condition still fulfilled => init all => wait
                    if ($time > 0) {
                        $this->SetTimerInterval('TPD.Timer', $time);
                        $this->WriteAttributeInteger('Trigger', ($sender == $id1) ? $sender : -1);
                    }
                    return false; // we wait
                }
                break;
        }
        // Timer has expired => Reset all
        $this->SendDebug(__FUNCTION__, 'Timer has expired!');
        $this->SetTimerInterval('TPD.Timer', 0);
        $this->WriteAttributeInteger('Trigger', 0);
        return false;
    }

    /**
     * Check brightness condition
     *
     * @return bool TRue if check successful; otherwise false.
     */
    private function CheckBrightness()
    {
        // Check brightness
        if ($this->ReadPropertyInteger('BrightnessVariable') != 0) {
            $bv = GetValue($this->ReadPropertyInteger('BrightnessVariable'));
            $tv = $this->ReadPropertyInteger('ThresholdValue');
            if ($this->ReadPropertyBoolean('ThresholdVariable')) {
                $tv = $this->GetValue('BrightnessThreshold');
            }
            if ($tv != 0 && $bv > $tv) {
                $this->SendDebug(__FUNCTION__, 'Above threshold: ' . $bv . '(Threshold: ' . $tv . ')');
                return false; // nothing to do
            }
            $this->SendDebug(__FUNCTION__, 'Always or below threshold: ' . $bv . ' (Threshold: ' . $tv . ')');
        }
        return true;
    }

    /**
     * Check weekly schedule
     *
     * @return bool TRue if check successful; otherwise false.
     */
    private function CheckSchedule()
    {
        // Check weekly schedule
        $eid = $this->ReadPropertyInteger('EventVariable');
        if ($eid != 0) {
            $state = $this->GetWeeklyScheduleInfo($eid);
            if ($state['WeekPlanActiv'] == 1 && $state['ActionID'] == 2) {
                $this->SendDebug(__FUNCTION__, 'Schedule plan is inactiv!');
                return false; // nothing to do
            }
        }
        return true;
    }

    /**
     * Switch Devices if deposite
     */
    private function SwitchDevices()
    {
        // Switch variable(s)
        $number = $this->ReadPropertyInteger('DeviceNumber');
        if ($number == self::DEVICE_ONE) {
            $dv = $this->ReadPropertyInteger('SwitchVariable');
            $this->SendDebug(__FUNCTION__, 'Switch only one device: ' . $dv);
            if ($dv != 0) {
                $ret = @RequestAction($dv, true);
                if ($ret === false) {
                    $this->SendDebug(__FUNCTION__, 'Device #' . $dv . ' could not be switched by RequestAction!');
                    $ret = @SetValueBoolean($dv, true);
                    if ($ret === false) {
                        $this->SendDebug(__FUNCTION__, 'Device could not be switched by Boolean!');
                    }
                }
                if ($ret === false) {
                    $this->LogMessage('Device could not be switched (UNREACH)!');
                }
            }
        } else {
            $variables = json_decode($this->ReadPropertyString('SwitchVariables'), true);
            $ret = true;
            foreach ($variables as $variable) {
                $this->SendDebug(__FUNCTION__, 'Switch multible devices: ' . $variable['VariableID']);
                $ret = @RequestAction($variable['VariableID'], true);
                if ($ret === false) {
                    $this->SendDebug(__FUNCTION__, 'Device #' . $variable['VariableID'] . ' could not be switched by RequestAction!');
                    $ret = false;
                }
            }
            if ($ret === false) {
                $this->LogMessage('One or more devices could not be switched!');
            }
        }
    }

    /**
     * Executes the script if deposited
     */
    private function ExecuteScript()
    {
        // Run script
        if ($this->ReadPropertyInteger('ScriptVariable') != 0) {
            if (IPS_ScriptExists($this->ReadPropertyInteger('ScriptVariable'))) {
                $mID = $this->ReadPropertyInteger('MotionVariable');
                $sID = $this->ReadPropertyInteger('SensorVariable');
                if ($sID != 0) {
                    $mID = '' . $mID . ',' . $sID;
                }
                $bID = $this->ReadPropertyInteger('BrightnessVariable');
                $dID = $this->ReadPropertyInteger('SwitchVariable');
                $tVA = $this->ReadPropertyInteger('ThresholdValue');
                if ($this->ReadPropertyBoolean('ThresholdVariable')) {
                    $tVA = $this->GetValue('BrightnessThreshold');
                }
                $number = $this->ReadPropertyInteger('DeviceNumber');
                if ($number == self::DEVICE_MULTIPLE) {
                    $variables = json_decode($this->ReadPropertyString('SwitchVariables'), true);
                    $dID = implode(',', array_column($variables, 'VariableID'));
                }
                $ret = IPS_RunScriptEx(
                    $this->ReadPropertyInteger('ScriptVariable'),
                    ['MotionVariable' => $mID, 'BrightnessVariable' => $bID, 'SwitchVariable' => $dID, 'ThresholdValue' => $tVA]
                );
                $this->SendDebug(__FUNCTION__, 'Script return value: ' . $ret);
            }
        }
    }

    /**
     * Received the status of a given variable
     *
     * @param int $vid variable ID.
     */
    private function GetVariableStatus($vid)
    {
        if (!IPS_VariableExists($vid)) {
            return $this->Translate('Missing');
        } else {
            $var = IPS_GetVariable($vid);
            switch ($var['VariableType']) {
                case VARIABLETYPE_BOOLEAN:
                    if ($var['VariableCustomProfile'] != '') {
                        $profile = $var['VariableCustomProfile'];
                    } else {
                        $profile = $var['VariableProfile'];
                    }
                    if (!IPS_VariableProfileExists($profile)) {
                        return $this->Translate('Profile required');
                    }
                    if ($var['VariableCustomAction'] != 0) {
                        $action = $var['VariableCustomAction'];
                    } else {
                        $action = $var['VariableAction'];
                    }
                    if (!($action > 10000)) {
                        return $this->Translate('Action required');
                    }
                    return 'OK';
                default:
                    return $this->Translate('Bool required');
            }
        }
    }

    /**
     * Creates a schedule plan.
     *
     * @param string $value instance ID.
     */
    private function OnCreateSchedule($value)
    {
        $eid = $this->CreateWeeklySchedule($this->InstanceID, self::SCHEDULE_NAME, self::SCHEDULE_IDENT, self::SCHEDULE_SWITCH, -1);
        if ($eid !== false) {
            $this->UpdateFormField('EventVariable', 'value', $eid);
        }
    }

    /**
     * User has select an new number of devices.
     *
     * @param string $value select value.
     */
    private function OnDeviceNumber($value)
    {
        $this->SendDebug(__FUNCTION__, 'Value: ' . $value);
        $this->UpdateFormField('SwitchVariable', 'visible', ($value == self::DEVICE_ONE));
        $this->UpdateFormField('SwitchVariables', 'visible', ($value == self::DEVICE_MULTIPLE));
    }
}
