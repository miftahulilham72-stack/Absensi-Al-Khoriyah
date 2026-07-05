DELIMITER //

DROP PROCEDURE IF EXISTS sp_absensi_masuk //

CREATE PROCEDURE sp_absensi_masuk(
    IN p_nis VARCHAR(20),
    IN p_ttd TEXT,
    OUT p_status VARCHAR(20),
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_peserta_id BIGINT;
    DECLARE v_sesi_id BIGINT;
    DECLARE v_batas_waktu TIME;
    DECLARE v_jam_sekarang TIME;
    DECLARE v_sudah_absen INT;
    DECLARE v_nama VARCHAR(100);
    
    -- 1. Cari peserta berdasarkan NIS
    SELECT id, nama_lengkap INTO v_peserta_id, v_nama 
    FROM peserta 
    WHERE nis = p_nis;
    
    IF v_peserta_id IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = CONCAT('NIS ', p_nis, ' tidak terdaftar! Silakan hubungi panitia.');
        LEAVE sp_absensi_masuk;
    END IF;
    
    -- 2. Cari sesi aktif
    SELECT id, batas_waktu INTO v_sesi_id, v_batas_waktu 
    FROM sesi 
    WHERE is_active = TRUE 
    LIMIT 1;
    
    IF v_sesi_id IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = 'Tidak ada sesi aktif saat ini!';
        LEAVE sp_absensi_masuk;
    END IF;
    
    -- 3. Cek apakah sudah absen (CEGAH DUPLIKAT)
    SELECT COUNT(*) INTO v_sudah_absen 
    FROM absensi 
    WHERE peserta_id = v_peserta_id AND sesi_id = v_sesi_id;
    
    IF v_sudah_absen > 0 THEN
        SET p_status = 'ERROR';
        SET p_message = CONCAT(v_nama, ' sudah melakukan absen untuk sesi ini!');
        LEAVE sp_absensi_masuk;
    END IF;
    
    -- 4. Cek TTD tidak boleh kosong
    IF p_ttd IS NULL OR p_ttd = '' OR LENGTH(p_ttd) < 50 THEN
        SET p_status = 'ERROR';
        SET p_message = 'Tanda tangan wajib diisi!';
        LEAVE sp_absensi_masuk;
    END IF;
    
    -- 5. Tentukan status
    SET v_jam_sekarang = CURTIME();
    
    IF v_jam_sekarang <= v_batas_waktu THEN
        SET p_status = 'Tepat Waktu';
    ELSE
        SET p_status = 'Terlambat';
    END IF;
    
    -- 6. Simpan absensi
    INSERT INTO absensi (peserta_id, sesi_id, jam_masuk, status, ttd_image) 
    VALUES (v_peserta_id, v_sesi_id, v_jam_sekarang, p_status, p_ttd);
    
    SET p_message = CONCAT('Halo ', v_nama, '! Absen berhasil. Status: ', p_status);
    
END //

DELIMITER ;