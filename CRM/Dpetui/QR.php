<?php

class CRM_Dpetui_QR {
  public static function getCode($participantId) {
    $contactHash = self::getContactHash($participantId);
    return hash('sha256', $participantId . $contactHash . CIVICRM_SITE_KEY);
  }

  private static function getContactHash($participantId) {
    $sql = "
      select
        hash
      from
        civicrm_contact c
      inner join
        civicrm_participant p on p.contact_id = c.id
      where
        p.id = $participantId
    ";

    $dao = CRM_Core_DAO::executeQuery($sql);
    if ($dao->fetch()) {
      return $dao->hash;
    }
    else {
      return '-INVALID-';
    }
  }
}

