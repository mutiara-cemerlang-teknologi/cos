<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Surat_jalan_m extends CI_Model 
{
    public function TampilData($id_pengguna = null)
    {
        $this->db->from('surat_jalan');
        if ($id_pengguna != null) {
            $this->db->where('ID_PENGGUNA ', $id_pengguna);
        }
        $this->db->order_by("ID_SURAT_JALAN", "DESC");
        $query = $this->db->get();
        return $query;
    }
    
    public function data_barang($id_barang)
    {
        $this->db->select('STOK_BARANG');
        $this->db->from('barang');
        $this->db->where('ID_BARANG  ', $id_barang);
        $query = $this->db->get();
        return $query;
    }
    
    public function SuratJalanById($id_surat_jalan)
    {
        return $this->db->get_where('surat_jalan', ['ID_SURAT_JALAN' => $id_surat_jalan]);
    }

    public function detailBarangSuratJalan($id_surat_jalan)
    {
        return $this->db->query(
                "
                SELECT * FROM detail_surat_jalan dsj 
                JOIN barang b ON b.ID_BARANG = dsj.ID_BARANG
                WHERE dsj.ID_SURAT_JALAN = '$id_surat_jalan'
                "
        );
    }
    
    public function noSJ()
    {
        $query = $this->db->query("SELECT MAX(NO_SURAT_JALAN) as nsj from surat_jalan");
        $hasil = $query->row();
        return $hasil->nsj;
    }
}

/* End of file Surat_jalan_m.php */
