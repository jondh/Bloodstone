<?php
App::uses('FormAuthenticate', 'Controller/Component/Auth');
/**
 * SaltFormAuthenticate: Authentication with salts
 *
 * @author nooclear.com
 */
class SaltFormAuthenticate extends FormAuthenticate {

    protected function _findUser($username, $password){
        $userModel = $this->settings['userModel'];
        list($plugin, $model) = pluginSplit($userModel);
        $fields = $this->settings['fields'];

        $conditions = array(
            $model . '.' . $fields['username'] => $username,
        );
        if (!empty($this->settings['scope'])) {
            $conditions = array_merge($conditions, $this->settings['scope']);
        }
        $result = ClassRegistry::init($userModel)->find('first', array(
            'conditions' => $conditions,
            'recursive' => 0,
            'fields' => array('*'),
                ));
        if (empty($result) || empty($result[$model])) {
            return false;
        }
        if($result[$model][$fields['password']] != $this->_password($password, $result[$model]['salt'])){
            return false;
        }
        unset($result[$model][$fields['password']]);
        unset($result[$model]['salt']);
        return $result[$model];
    }

    protected function _password($password, $salt){
        return Security::hash(Security::hash(Security::hash($password.$salt)));
    }
}
?>