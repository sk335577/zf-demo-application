<?php

namespace AuthGuardManager;

use Employee\Model\EmployeeTable;
use Main\Form\LoginForm;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Sql\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;

class AuthController extends AbstractActionController {

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @var EmployeeTable
     */
    private $employeeTable;

    /**
     * AuthController constructor.
     *
     * @param AuthenticationService $authService
     * @param EmployeeTable $employeeTable
     */
    public function __construct(AuthenticationService $authService, EmployeeTable $employeeTable) {
        $this->authService = $authService;
        $this->employeeTable = $employeeTable;
    }

    /**
     * @return ViewModel
     */
    public function loginAction() {

        $form = new LoginForm();

        $request = $this->getRequest();

        if (!$request->isPost()) {
            return ['form' => $form];
        }

        $form->setData($request->getPost());
        if (!$form->isValid()) {
            $this->flashMessenger()->addMessage("your error message", FlashMessenger::NAMESPACE_ERROR);

            return ['form' => $form];
        }

        /** @var Employee $employeeObject */
        $employeeObject = $this->employeeTable->getEmployeeByLogin($request->getPost('system_name'), $request->getPost('password'));
        if (!$employeeObject) {
            $this->flashMessenger()->addMessage("your error message", FlashMessenger::NAMESPACE_ERROR);

            return ['form' => $form];
        }

        if ($this->authenticate($employeeObject)) {
            return $this->redirect()->toRoute('main');
        }

        $this->flashMessenger()->addMessage("your error message", FlashMessenger::NAMESPACE_ERROR);

        return ['form' => $form];
    }

    public function logoutAction() {
        $this->authService->clearIdentity();

        return $this->redirect()->toRoute('login');
    }

    /**
     * @param $employeeObject
     *
     * @return array
     */
    private function authenticate($employeeObject) {
        /** @var CredentialTreatmentAdapter $adapter */
        $adapter = $this->authService->getAdapter();
        $select = $adapter->getDbSelect();
        $select->join(['R' => 'role'], 'role_id = R.id', ['role' => 'description'], Select::JOIN_LEFT);
        $this->authService->setAdapter($adapter);

        $this->authService->getAdapter()->setIdentity($employeeObject->system_name)->setCredential($employeeObject->password);
        $result = $this->authService->authenticate();

        if ($result->isValid()) {
            $resultRow = $this->authService->getAdapter()->getResultRowObject();

            $this->authService->getStorage()->write([
                'id' => $resultRow->id,
                'system_name' => $resultRow->system_name,
                'role' => $resultRow->role,
            ]);

            return true;
        }

        return false;
    }

}
