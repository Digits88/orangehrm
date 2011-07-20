<?php

class LeaveListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected static $listMode;
    
    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new ListHeader();
        $header2 = new ListHeader();
        $header3 = new ListHeader();
        $header4 = new ListHeader();
        $header5 = new ListHeader();
        $header6 = new ListHeader();
        $header7 = new LeaveListActionHeader();

        $header1->populateFromArray(array(
            'name' => 'Date',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => array('getLeaveDateRange'),
                'placeholderGetters' => array('id' => 'getLeaveRequestId'),
                'urlPattern' => public_path('index.php/leave/viewLeaveList/id/{id}'),
            ),
        ));

        $header2->populateFromArray(array(
            'name' => 'Employee Name',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => array('getEmployee', 'getFullName'),
                'placeholderGetters' => array('id' => 'getEmployeeId'),
                'urlPattern' => public_path('index.php/pim/viewPersonalDetails/empNumber/{id}'),
            ),
        ));

        $header3->populateFromArray(array(
            'name' => 'Leave Type',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'label',
            'elementProperty' => array('getter' => array('getLeaveType', 'getDescriptiveLeaveTypeName')),
        ));

        $header4->populateFromArray(array(
            'name' => 'Number of Days',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getNumberOfDays'),
        ));

        $header5->populateFromArray(array(
            'name' => 'Status',
            'width' => '10%',
            'isSortable' => false,
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => array('getStatus'),
                'placeholderGetters' => array('id' => 'getLeaveRequestId'),
                'urlPattern' => public_path('index.php/leave/viewLeaveList/id/{id}'),
            ),
        ));

        $header6->populateFromArray(array(
            'name' => 'Comments',
            'width' => '20%',
            'isSortable' => false,
            'elementType' => 'comment',
            'elementProperty' => array(
                'getter' => 'getLeaveComments',
                'idPattern' => 'hdnLeaveComment-{id}',
                'namePattern' => 'leaveComments[{id}]',
                'placeholderGetters' => array('id' => 'getLeaveRequestId'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leaveRequest[{id}]',
                'hiddenFieldId' => 'hdnLeaveRequest_{id}',
                'hiddenFieldValueGetter' => 'getLeaveRequestId',
            ),
        ));

        
        $leaveRequestService = new LeaveRequestService();
        $header7->populateFromArray(array(
            'name' => 'Actions',
            'width' => '10%',
            'isSortable' => false,
            'isExportable' => false,
            'elementType' => 'leaveListAction',
            'elementProperty' => array(
                'defaultOption' => array('label' => 'Select Action', 'value' => ''),
                'hideIfEmpty' => true,
                'options' => array($leaveRequestService, 'getLeaveRequestActions', array(self::RECORD, self::$userId, self::$listMode)),
                'namePattern' => 'select_leave_action_{id}',
                'idPattern' => 'select_leave_action_{id}',
                'placeholderGetters' => array(
                    'id' => 'getLeaveRequestId',
                ),
            ),
        ));

        $this->headers = array($header1, $header2, $header3, $header4, $header5, $header6, $header7);
    }
    
    public function getClassName() {
        return 'LeaveRequest';
    }
    
    public static function setListMode($listMode) {
        self::$listMode = $listMode;
    }
}