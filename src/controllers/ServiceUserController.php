<?php
/**
 * Created by PhpStorm.
 * User: john.ogrady
 * Date: 18/02/2017
 * Time: 22:24
 */

namespace Itb\Controller;

use Itb\model\AssignedEmployee;
use Itb\Model\AssignedEmployeeRepository;
use Itb\Model\DoNotSend;
use Itb\Model\DoNotSendRepository;
use Itb\model\EmployeeRepository;
use Itb\Model\ServiceUser;
use Itb\model\ServiceUserRepository;
use Itb\model\ServiceUserRepositoryView;
use Itb\model\RosterRepositoryView;
use Itb\Model\LookUpReferenceRepositoryCounties;
use Itb\model\OfficeRepository;
use Itb\WebApplication;

class ServiceUserController
{
    private $app;

    public function __construct(WebApplication $app)
    {
        $this->app = $app;
    }

    public function listAction()
    {
        // get reference to our repository
        $ServiceUserRepository= new ServiceUserRepositoryView();
        $serviceUsers= $ServiceUserRepository->getAll();

        $argsArray = [
            'serviceUsers' => $serviceUsers
        ];
          $templateName = 'ServiceUsers\list';
              return $this->app['twig']->render($templateName . '.html.twig', $argsArray);
    }

    public function successAction()
    {
        $templateName = 'ServiceUsers\success';
        return $this->app['twig']->render($templateName . '.html.twig');
    }

    public function updateAction($id)
    {
        // get reference to our repository
        $ServiceUserRepository= new ServiceUserRepository();
        // get array of product attributes for that product, ready for view to use to populate form
        $serviceUser= $ServiceUserRepository->getOneById($id);
        // database connection
        $counties= new LookUpReferenceRepositoryCounties();

         $counties = $counties->getAll();

        $offices= new OfficeRepository();

        $offices= $offices->getAll();


         if (null == $serviceUser) {
            $message = 'sorry, no ServiceUser with id = ' . $id . ' could be retrieved from the database';
            $templateName = 'message';

            return $this->app['twig']->render($templateName . '.html.twig');
        } else {
            // route user to update page for product
            // output the detail of product in HTML table
           $templateName = 'ServiceUsers\update';
            $argsArray = [
                'serviceUser' => $serviceUser,
                'counties' => $counties,
                'offices' => $offices
            ];

            return $this->app['twig']->render($templateName . '.html.twig',$argsArray);
        }

    }

    public function processUpdateAction()
    {
        // get reference to our repository
        $editedServiceUser= new ServiceUser();
        $editedServiceUser->setId(filter_input(INPUT_POST, 'Id'));
        $editedServiceUser->setFirstName(filter_input(INPUT_POST, 'firstName'));
        $editedServiceUser->setLastName(filter_input(INPUT_POST, 'lastName'));
        $editedServiceUser->setAddressLine1(filter_input(INPUT_POST, 'addressLine1'));
        $editedServiceUser->setAddressLine2(filter_input(INPUT_POST, 'addressLine2'));
        $editedServiceUser->setAddressLine3(filter_input(INPUT_POST, 'addressLine3'));
        if (isset($_POST['startDate']))
            $editedServiceUser->setIsActive(filter_input(INPUT_POST, 'startDate'));
        if (isset($_POST['finishDate']))
            $editedServiceUser->setIsActive(filter_input(INPUT_POST, 'finishDate'));
        $editedServiceUser->setCountyPostcode(filter_input(INPUT_POST, 'countyPostcode'));
        $editedServiceUser->setEirCode(filter_input(INPUT_POST, 'eirCode'));
        $editedServiceUser->setMobileTelephone(filter_input(INPUT_POST, 'mobileTelephone'));
        $editedServiceUser->setLandlineTelephone(filter_input(INPUT_POST, 'landlineTelephone'));
        $editedServiceUser->setManagingOffice(filter_input(INPUT_POST, 'managingOffice'));
        if (isset($_POST['isActive']))
            $editedServiceUser->setIsActive(1);
        else
            $editedServiceUser->setIsActive(0);
        $ServiceUserRepo= new ServiceUserRepository();
        $success = $ServiceUserRepo->update($editedServiceUser);
        $templateName = 'ServiceUsers\success';
        if($success){
            $id = $editedServiceUser->getId(); // get ID of new record
            $message = "SUCCESS -  ServiceUser with ID = ".$id." updated";
        } else {
            $message = 'sorry, there was a problem updating that Service User';
        }
        // route user to message page with success or failure notice

        $argsArray = [  'message' => $message];
        return $this->app['twig']->render($templateName . '.html.twig', $argsArray);
    }



    public function deleteAction($id)
    {
        // get reference to our repository
        $ServiceUserRepository= new ServiceUserRepository();
        $success = $ServiceUserRepository->delete($id);
        if($success){
            $message = "SUCCESS - ServiceUser deleted";
        } else {
            $message = 'sorry, there was a problem deleting that ServiceUser';
        }
        // route user to message page with success or failure notice
        $templateName = 'ServiceUser\success';
        $argsArray = [  'message' => $message];
        return $this->app['twig']->render($templateName . '.html.twig', $argsArray);
    }

    public function createAction()
    {
        // get reference to our repository
        $counties= new LookUpReferenceRepositoryCounties();

        $counties = $counties->getAll();
        //to do update to get one by id

        $offices= new OfficeRepository();

        $offices= $offices->getAll();
        //to do update to get one by id
        $argsArray = [
            'counties' => $counties,
            'offices' => $offices
        ];
           $templateName = 'ServiceUsers\create';
        return $this->app['twig']->render($templateName . '.html.twig', $argsArray);
    }


    public function processCreateAction()
    {
        // get reference to our repository
        $newServiceUser= new ServiceUser();
        $newServiceUser->setFirstName(filter_input(INPUT_POST, 'firstName'));
        $newServiceUser->setLastName(filter_input(INPUT_POST, 'lastName'));
        $newServiceUser->setAddressLine1(filter_input(INPUT_POST, 'addressLine1'));
        $newServiceUser->setAddressLine2(filter_input(INPUT_POST, 'addressLine2'));
        $newServiceUser->setAddressLine3(filter_input(INPUT_POST, 'addressLine3'));
        $newServiceUser->setCountyPostcode(filter_input(INPUT_POST, 'countyPostcode'));
        $newServiceUser->setEirCode(filter_input(INPUT_POST, 'eirCode'));
        $newServiceUser->setMobileTelephone(filter_input(INPUT_POST, 'mobileTelephone'));
        $newServiceUser->setLandlineTelephone(filter_input(INPUT_POST, 'landlineTelephone'));
        $newServiceUser->setManagingOffice(filter_input(INPUT_POST, 'managingOffice'));

        $newServiceUser->getId();
        $serviceUserRepo= new ServiceUserRepository();
        $success = $serviceUserRepo->create($newServiceUser);
        $templateName = 'ServiceUsers\success';
        if($success){
            $id = $newServiceUser->getId(); // get ID of new record
            $message = "SUCCESS - new service user ".$newServiceUser->getFirstName()." ".$newServiceUser->getLastName()." has been created";
        } else {
            $message = 'sorry, there was a problem creating new customer';
        }
        // route user to message page with success or failure notice

        $argsArray = [  'message' => $message];
        return $this->app['twig']->render($templateName . '.html.twig', $argsArray);
    }

    public function showAction($id)
    {
        // get reference to our repository
        $ServiceUserRepository = new ServiceUserRepositoryView();
        $serviceUser = $ServiceUserRepository->getOneById($id);
        $RosterRepository = new RosterRepositoryView();
        $foreignKey="serviceuserid";
        $rosters= $RosterRepository->getAllForId($id,$foreignKey);
        //var_dump($rosters);
         //to do update to get one by id
        // get array of attributes for that customer, ready for view to use to populate form
        $argsArray = [
            'serviceUser' => $serviceUser,
            'rosters'=>$rosters
        ];


        $templateName = 'ServiceUsers\show';
        return $this->app['twig']->render($templateName . '.html.twig', $argsArray);
    }

    public function addDoNotSendAction($id)
    {
        // get reference to our repository
        $ServiceUserRepository= new ServiceUserRepository();
        $serviceUser= $ServiceUserRepository->getOneById($id);
        $employees= new EmployeeRepository();
        $employees=$employees->getAll();


        if (null == $serviceUser) {
            $message = 'sorry, no ServiceUser with id = ' . $id . ' could be retrieved from the database';
            $templateName = 'message';

            return $this->app['twig']->render($templateName . '.html.twig');
        } else {
            // route user to update page for product
            // output the detail of product in HTML table
            $templateName = 'ServiceUsers\addDoNotSend';
            $argsArray = [
                'id' => $id,
                'serviceUser'=>$serviceUser,
                'employees'=>$employees
            ];

            return $this->app['twig']->render($templateName . '.html.twig',$argsArray);
        }

    }



    public function processDoNotSendAction()
    {

        $doNotSend= new DoNotSend();
        $doNotSend->setEmployeeId(filter_input(INPUT_POST, 'employeeId'));
        $doNotSend->setServiceUserId(filter_input(INPUT_POST, 'serviceuser'));
        $doNotSend->getId();
        $doNotSendRepo= new DoNotSendRepository();
        $success = $doNotSendRepo->create($doNotSend);
        $templateName = 'ServiceUsers\success';
        if($success){
            $id = $doNotSend->getId(); // get ID of new record
            $message = "SUCCESS - this employee has been marked as DO NOT SEND ";
        } else {
            $message = 'sorry, there was a problem, this employee has NOT marked as DO NOT SEND';
        }
        // route user to message page with success or failure notice

        $argsArray = [  'message' => $message];
        return $this->app['twig']->render($templateName . '.html.twig', $argsArray);
    }



    public function assignEmployeeAction($id)
    {
        // get reference to our repository
        $ServiceUserRepository= new ServiceUserRepository();
        $serviceUser= $ServiceUserRepository->getOneById($id);
        $employees= new EmployeeRepository();
        $employees=$employees->getAll();


        if (null == $serviceUser) {
            $message = 'sorry, no ServiceUser with id = ' . $id . ' could be retrieved from the database';
            $templateName = 'message';

            return $this->app['twig']->render($templateName . '.html.twig');
        } else {
            // route user to update page for product
            // output the detail of product in HTML table
            $templateName = 'ServiceUsers\assignEmployee';
            $argsArray = [
                'id' => $id,
                'serviceUser'=>$serviceUser,
                'employees'=>$employees
            ];

            return $this->app['twig']->render($templateName . '.html.twig',$argsArray);
        }

    }



    public function processAssignEmployeeAction()
    {

        $assignedEmployee= new AssignedEmployee();
        $assignedEmployee->setEmployeeId(filter_input(INPUT_POST, 'employeeId'));
        $assignedEmployee->setServiceUserId(filter_input(INPUT_POST, 'serviceuser'));
        $assignedEmployee->getId();
        var_dump($assignedEmployee);
        $assignedEmployeeRepo= new AssignedEmployeeRepository();
        $success = $assignedEmployeeRepo->create($assignedEmployee);
        $templateName = 'ServiceUsers\success';
        if($success){
            $id = $assignedEmployee->getId(); // get ID of new record
            $message = "SUCCESS - this employee has been assigned";
        } else {
            $message = 'sorry, there was a problem, this employee has not assigned';
        }
        // route user to message page with success or failure notice

        $argsArray = [  'message' => $message];
        return $this->app['twig']->render($templateName . '.html.twig', $argsArray);
    }

}