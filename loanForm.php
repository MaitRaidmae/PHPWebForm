<?php
/**
 * Description of loanForm
 *
 * @author Hundipesa
 */
class loanForm {
    private $name;
    private $personal_code;
    private $amount;
    private $period;
    private $purpose;
    private $errors = [];
    private $outputFilePath = 'submitted_forms' . DIRECTORY_SEPARATOR;
    private $filePath;
 
    public function __construct($formFields) {
        $this->name          = filter_var($formFields['name'],FILTER_SANITIZE_SPECIAL_CHARS);
        $this->personal_code = filter_var($formFields['personal_code'],FILTER_SANITIZE_NUMBER_INT);
        $this->amount        = filter_var($formFields['amount'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION); 
        $this->period        = filter_var($formFields['period'],FILTER_SANITIZE_NUMBER_INT);
        $this->purpose       = filter_var($formFields['purpose'],FILTER_SANITIZE_SPECIAL_CHARS);
        $this->errors        = $this->validateFields();
    }
        
    public function getErrors(){
        return $this->errors;
    }
    
    private function validateFields() {
        $errors = array();
        if ($this->name == '' || strPos($this->name,' ') == false) {
            $errors['name'] = "Palun sisesta ees ja perekonna nimi.";
        }
        
        $validatePersonalCodeResult = $this->validatePersonalCode($this->personal_code);
        if ($validatePersonalCodeResult != 'OK') {
            $errors['personal_code'] = $validatePersonalCodeResult;
        }
        
        if ($this->amount < 1000 || $this->amount > 10000) {
            $errors['amount'] = "Laenu summa peab olema vahemikus 1000 kuni 10000 eurot.";
        }
        
        if ($this->period < 6 || $this->period > 24) {            
            $errors['period'] = "Laenu periood peab olema vahemikus 6 kuni 24 kuud.";
        }
        
        $validatePurposeResult = $this->validatePurpose($this->purpose);
        if ($validatePurposeResult != 'OK') {
            $errors['purpose'] = $validatePurposeResult;
        }
        return $errors;
    }    
   
    public function printToFile() {
       $saveArray = array(
           'name'          => $this->name,
           'personal_code' => $this->personal_code,
           'amount'        => $this->amount,
           'period'        => $this->period,
           'purpose'       => $this->purpose,
           'timestamp'     => filter_input(INPUT_SERVER,'REQUEST_TIME')
       );
       $saveJson = json_encode($saveArray);
       if (!file_exists($this->outputFilePath)) {
           mkdir($this->outputFilePath);
       }
       $filePath = $this->outputFilePath . "FormSubmit_" . $this->personal_code . "_" . time();
       file_put_contents($filePath, $saveJson);
       $this->filePath = $filePath;
       return $filePath;
    }
    
    public function getFilePath() {
        return $this->filePath;
    }
    
    private function validatePersonalCode($personalCode){
        if (strlen($personalCode) != 11) {
            return "Isikukoodi pikkus peab olema 11 numbrit.";
        }
        $checkValue  = substr($personalCode, 10, 1);
        $weigths     = [1,2,3,4,5,6,7,8,9,1];
        $weights_ext = [3,4,5,6,7,8,9,1,2,3];
        $sum = $this->getSum($personalCode,$weigths);
        $mod = $sum % 11;
        if ($mod == 10) {
            $sum = $this->getSum($personalCode,$weights_ext);
            $mod = $sum % 11;
        }
        if ($mod == 10){
            $mod = 0;
        }        
        if ($mod != $checkValue) {
            return "Isikukoodis on viga - kontrollnumbri kontroll ebaõnnestus.";
        }           
        
        return "OK";
    }
    
    private function getSum($personalCode,$weights){
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += substr($personalCode,$i,1) * $weights[$i];
        }           
        return $sum;
    }
    
    private function validatePurpose($purpose){
       $validStrings = ['puhkus', 'remont', 'koduelektroonika', 'pulmad', 'rent', 'auto', 'kool', 'investeering'];
       
       forEach ($validStrings as $string){
           if (strpos(strtolower($purpose), $string) !== false) {
               return "OK";
           }
       }               
       return "Kasutuseesmärk peaks olema üks järgnevatest: puhkus, remont, koduelektroonika, pulmad, rent, auto, kool, investeering."; 
    }
}
