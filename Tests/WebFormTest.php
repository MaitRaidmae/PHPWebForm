<?php
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__) . '/../loanForm.php');

class WebformTest extends TestCase {
    
    private $correctArray = [
            'name'          => "Peter The Great",
            'personal_code' => 51107121760, 
            'amount'        => 2000,
            'period'        => 24,
            'purpose'       => "Puhkus"
        ];  
    
     public function testCorrectForm() {
        $testArray = $this->correctArray;        
        $loanForm = new loanForm($testArray);
        $errors = $loanForm->getErrors();
        $this->assertSame(0,count($errors));
    } 
    
    public function testIncorrectPersonalCodeCheckCode() {
        $testArray = $this->correctArray;
        $testArray['personal_code'] = 51107121761; /*Correct personal code is 51107121760*/        
        $loanForm = new loanForm($testArray);
        $errors = $loanForm->getErrors();
        $this->assertSame('Isikukoodis on viga - kontrollnumbri kontroll ebaõnnestus.',$errors['personal_code']);
    }
    
    public function testIncorrectPersonalCodeLength() {
        $testArray = $this->correctArray;
        $testArray['personal_code'] = 5110712176; /*Correct personal code is 51107121760*/
        $loanForm = new loanForm($testArray);
        $errors = $loanForm->getErrors();
        $this->assertSame('Isikukoodi pikkus peab olema 11 numbrit.',$errors['personal_code']);
    }
    
    public function testIncorrectName() {
        $testArray = $this->correctArray;
        $testArray['name'] = "Peter";       
        $loanForm = new loanForm($testArray);
        $errors = $loanForm->getErrors();
        $this->assertSame('Palun sisesta ees ja perekonna nimi.',$errors['name']);
    }
    
    public function testLowAmount() {
        $testArray = $this->correctArray;
        $testArray['amount'] = 999.99;        
        $loanForm = new loanForm($testArray);
        $errors = $loanForm->getErrors();
        $this->assertSame('Laenu summa peab olema vahemikus 1000 kuni 10000 eurot.',$errors['amount']);
    }    
    
    public function testHighAmount() {
        $testArray = $this->correctArray;
        $testArray['amount'] = 10000.01;        
        $loanForm = new loanForm($testArray);
        $errors = $loanForm->getErrors();
        $this->assertSame('Laenu summa peab olema vahemikus 1000 kuni 10000 eurot.',$errors['amount']);
    }
    
    public function testLowPeriod() {
        $testArray = $this->correctArray;
        $testArray['period'] = 5;        
        $loanForm = new loanForm($testArray);
        $errors = $loanForm->getErrors();
        $this->assertSame('Laenu periood peab olema vahemikus 6 kuni 24 kuud.',$errors['period']);
    }
    
    public function testHighPeriod() {
        $testArray = $this->correctArray;
        $testArray['period'] = 25;        
        $loanForm = new loanForm($testArray);
        $errors = $loanForm->getErrors();
        $this->assertSame('Laenu periood peab olema vahemikus 6 kuni 24 kuud.',$errors['period']);
    }
    
    public function testInvalidPurpose() {
        $testArray = $this->correctArray;
        $testArray['purpose'] = "Test Failed Purpose";        
        $loanForm = new loanForm($testArray);
        $errors = $loanForm->getErrors();
        $this->assertSame('Kasutuseesmärk peaks olema üks järgnevatest: puhkus, remont, koduelektroonika, pulmad, rent, auto, kool, investeering.',$errors['purpose']);
    }
    
    public function testFileCreated() {
      $testArray = $this->correctArray;
      $loanForm = new loanForm($testArray);
      $filePath = $loanForm->printToFile();      
      $this->assertFileExists($filePath);
      unlink($filePath);
    }
    
}
