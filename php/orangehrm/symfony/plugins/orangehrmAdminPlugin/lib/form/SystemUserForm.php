<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */
class SystemUserForm extends BaseForm {

        private $userId = null;
	private $systemUserService;
	public $edited = false;
	
        public function getSystemUserService() {
            $this->systemUserService = new SystemUserService();
            return $this->systemUserService;
        }

        

        	
	public function configure() {

		$this->userId   =   $this->getOption('userId');
                if(!empty($this->userId)){
                    $this->edited = true ;
                }
		$userRoleList   =   $this->getPreDefinedUserRoleList();
		$statusList     =   $this->getStatusList();

		$this->setWidgets(array(
		    'userId' => new sfWidgetFormInputHidden(),
		    'userType' => new sfWidgetFormSelect(array('choices' => $userRoleList)),
                    'employeeName' => new sfWidgetFormInputText(),
                    'employeeId' => new sfWidgetFormInputHidden(),
		    'userName' => new sfWidgetFormInputText(),
		    'password' => new sfWidgetFormInputPassword(),
                    'confirmPassword' => new sfWidgetFormInputPassword(),
		    'status' => new sfWidgetFormSelect(array('choices' => $statusList)),
		   
		));

		$this->setValidators(array(
		    'userId' => new sfValidatorNumber(array('required' => false)),
		    'userType' => new sfValidatorString(array('required' => true, 'max_length' => 3)),
                    'employeeName' => new sfValidatorString(array('required' => true, 'max_length' => 200)),
                    'employeeId' => new sfValidatorString(array('required' => true)),
		    'userName' => new sfValidatorString(array('required' => true, 'max_length' => 20)),
		    'password' => new sfValidatorString(array('required' => false, 'max_length' => 20)),
		    'confirmPassword' => new sfValidatorString(array('required' => false, 'max_length' => 20)),
		    'status' => new sfValidatorString(array('required' => true, 'max_length' => 1)),
		));


		$this->widgetSchema->setNameFormat('systemUser[%s]');
		
		if ($this->userId != null) {
			$this->setDefaultValues($this->userId);
		}
	}
	
	private function setDefaultValues($locationId) {

                $systemUser   =   $this->getSystemUserService()->getSystemUser( $this->userId );
		
		$this->setDefault('userId', $systemUser->getId());
		$this->setDefault('userType', $systemUser->getUserRoleId());
		$this->setDefault('employeeName', $systemUser->getEmployee()->getFullName());
		$this->setDefault('employeeId', $systemUser->getEmpNumber());
		$this->setDefault('userName', $systemUser->getUserName());
                $this->setDefault('status', $systemUser->getStatus());
		
	}

	/**
	 * Get Pre Defined User Role List
         * 
	 * @return array
	 */
	private function getPreDefinedUserRoleList() {
		$list = array();
		$userRoles = $this->getSystemUserService()->getPreDefinedUserRoles();
		foreach ($userRoles as $userRole) {
			$list[$userRole->getId()] = $userRole->getName();
		}
		return $list;
	}
        
        private function getStatusList(){
            $list = array();
            $list[1] = __("Enabled");
            $list[0] = __("Disabled");
            
            return $list;
        }

	

	public function save() {

		$userId = $this->getValue('userId');
		if(empty($userId)){
			$user = new SystemUser();
		} else {
			$this->edited = true;
			$user = $this->getSystemUserService()->getSystemUser( $userId );
		}
                
		$user->setUserRoleId( $this->getValue('userType'));
                $user->setEmpNumber( $this->getValue('employeeId') );
                $user->setUserName( $this->getValue('userName'));
                $user->setUserPassword( $this->getValue('password'));
                $user->setStatus( $this->getValue('status'));
               
		$this->getSystemUserService()->saveSystemUser( $user );
		
	}
        
        public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        
        $employeeList = $employeeService->getEmployeeList('empNumber', 'ASC', true);

        $employeeUnique = array();
        foreach ($employeeList as $employee) {
            $workShiftLength = 0;

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {
                
                $name = $employee->getFullName();

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }


	
}

?>