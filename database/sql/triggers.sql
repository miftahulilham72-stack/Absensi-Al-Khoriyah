-- Cegah penghapusan data absensi
DELIMITER //

DROP TRIGGER IF EXISTS trg_absensi_before_delete //

CREATE TRIGGER trg_absensi_before_delete
BEFORE DELETE ON absensi
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' 
    SET MESSAGE_TEXT = 'Data absensi tidak dapat dihapus! Hubungi Administrator.';
END //

DELIMITER ;

-- Cegah update setelah 5 menit (cegah manipulasi)
DELIMITER //

DROP TRIGGER IF EXISTS trg_absensi_before_update //

CREATE TRIGGER trg_absensi_before_update
BEFORE UPDATE ON absensi
FOR EACH ROW
BEGIN
    IF TIMESTAMPDIFF(MINUTE, OLD.created_at, NOW()) > 5 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Tidak dapat mengubah absensi setelah 5 menit!';
    END IF;
END //

DELIMITER ;