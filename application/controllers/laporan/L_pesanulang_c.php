<?php

defined('BASEPATH') or exit('No direct script access allowed');



class L_pesanulang_c extends CI_Controller

{

    public function __construct()

    {

        parent::__construct();

        is_logged_in();

        $this->load->model('sales/Surat_jalan_m', 'surat_jalan_m');

        $this->load->model('gudang/Barang_m', 'barang_m');

        $this->load->model('sales/Penjualan_m', 'penjualan_m');

        $this->load->model('sales/Pelanggan_m', 'pelanggan_m');

        $this->load->model('sales/Pesan_ulang_m', 'pesan_ulang_m');

    }



    public function index()

    {

        $data['title'] = "Laporan Pesan Ulang";



        $data['user'] = $this->db->get_where('pengguna', ['EMAIL_PENGGUNA' => $this->session->userdata('email')])->row_array();



        $data['pesanulang'] = $this->pesan_ulang_m->TampilData()->result();

        $data['pelanggan'] = $this->pelanggan_m->TampilData()->result();



        $this->load->view('templates/header', $data);

        $this->load->view('templates/sidebar', $data);

        $this->load->view('templates/topbar', $data);

        $this->load->view('laporan/pesanulang', $data);

        $this->load->view('templates/footer');

    }



    public function view()

    {

        $tgaw = $this->input->post('tgaw');

        $tgak = $this->input->post('tgak');

        $user = $this->db->get_where('pengguna', ['EMAIL_PENGGUNA' => $this->session->userdata('email')])->row();
        $id = $user->ID_PENGGUNA;
        $datapnj1 = $this->db->query(
                "
               SELECT * FROM pesan_ulang pu
            JOIN detail_pesan_ulang dpu ON dpu.ID_PESAN_ULANG = pu.ID_PESAN_ULANG
            JOIN pelanggan p ON p.ID_PELANGGAN = pu.ID_PELANGGAN
            JOIN pengguna pg ON pg.ID_PENGGUNA = pu.ID_PENGGUNA
            JOIN barang b ON b.ID_BARANG = dpu.ID_BARANG
            WHERE pu.ID_PENGGUNA = '$id' AND pu.TGL_PESAN_ULANG BETWEEN '$tgaw' AND '$tgak'
                "
            )->num_rows();
        if($datapnj1 > 0){
            $datapnj = $this->db->query(
                "
               SELECT * FROM pesan_ulang pu
            JOIN detail_pesan_ulang dpu ON dpu.ID_PESAN_ULANG = pu.ID_PESAN_ULANG
            JOIN pelanggan p ON p.ID_PELANGGAN = pu.ID_PELANGGAN
            JOIN pengguna pg ON pg.ID_PENGGUNA = pu.ID_PENGGUNA
            JOIN barang b ON b.ID_BARANG = dpu.ID_BARANG
            WHERE pu.ID_PENGGUNA = '$id' AND pu.TGL_PESAN_ULANG BETWEEN '$tgaw' AND '$tgak'
                "
            )->result();
        }else{
            $datapnj = $this->db->query(
                "
               SELECT * FROM pesan_ulang pu
            JOIN detail_pesan_ulang dpu ON dpu.ID_PESAN_ULANG = pu.ID_PESAN_ULANG
            JOIN pelanggan p ON p.ID_PELANGGAN = pu.ID_PELANGGAN
            JOIN pengguna pg ON pg.ID_PENGGUNA = pu.ID_PENGGUNA
            JOIN barang b ON b.ID_BARANG = dpu.ID_BARANG
            WHERE pu.TGL_PESAN_ULANG BETWEEN '$tgaw' AND '$tgak'
                "
            )->result();
        }
        /*$datapnj = $this->db->query(

            "

            SELECT * FROM pesan_ulang pu

            JOIN detail_pesan_ulang dpu ON dpu.ID_PESAN_ULANG = pu.ID_PESAN_ULANG

            JOIN pelanggan p ON p.ID_PELANGGAN = pu.ID_PELANGGAN

            JOIN pengguna pg ON pg.ID_PENGGUNA = pu.ID_PENGGUNA

            JOIN barang b ON b.ID_BARANG = dpu.ID_BARANG

            WHERE pu.TGL_PESAN_ULANG BETWEEN '$tgaw' AND '$tgak'

            "

        )->result();*/

    ?>

    <div class="col-lg-12 col-sm-12">

        <div class="card shadow mb-4">

            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">

                <h6 class="m-0 font-weight-bold text-primary">Data Pesan Ulang</h6>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

                        <thead>

                            <tr>

                                <th>No.</th>

                                <th>Nama Pelanggan</th>

                                <th>Nama Sales</th>

                                <th>Tanggal Pesan Ulang</th>

                                <th>Nama Barang</th>

                                <th>Jumlah</th>

                                <th>Total</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php

                            $i = 1;

                            foreach ($datapnj as $dpj) : ?>

                                <tr>

                                    <td><?php echo $i ?></td>

                                    <td><?php echo $dpj->NAMA_PELANGGAN ?></td>

                                    <td><?php echo $dpj->NAMA_PENGGUNA ?></td>

                                    <td><?php echo date_indo($dpj->TGL_PESAN_ULANG) ?></td>

                                    <td><?php echo $dpj->NAMA_BARANG ?></td>

                                    <td><?php echo $dpj->JUMLAH_PESAN_ULANG ?></td>

                                    <td>Rp. <?php echo number_format($dpj->HARGA_JUAL_BARANG*$dpj->JUMLAH_PESAN_ULANG) ?></td>

                                </tr>

                            <?php

                                $i++;

                            endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <div class="text-right">

                    <button class="btn btn-success mr-1" type="submit">Cetak Data</button>

                </div>

            </div>

        </div>

    </div>



        

    <?php

    }



    public function cetak()

    {

        $this->load->library('Pdf');

        $tgaw = $this->input->post('tgaw');

        $tgak = $this->input->post('tgak');
        $tgl = date('Y-m-d');

        $user = $this->db->get_where('pengguna', ['EMAIL_PENGGUNA' => $this->session->userdata('email')])->row();
        $id = $user->ID_PENGGUNA;
        $pesanulang1 = $this->db->query(
                "
               SELECT * FROM pesan_ulang pu
            JOIN detail_pesan_ulang dpu ON dpu.ID_PESAN_ULANG = pu.ID_PESAN_ULANG
            JOIN pelanggan p ON p.ID_PELANGGAN = pu.ID_PELANGGAN
            JOIN pengguna pg ON pg.ID_PENGGUNA = pu.ID_PENGGUNA
            JOIN barang b ON b.ID_BARANG = dpu.ID_BARANG
            WHERE pu.ID_PENGGUNA = '$id' AND pu.TGL_PESAN_ULANG BETWEEN '$tgaw' AND '$tgak'
                "
            )->num_rows();
        if($pesanulang1 > 0){
            $pesanulang = $this->db->query(
                "
               SELECT * FROM pesan_ulang pu
            JOIN detail_pesan_ulang dpu ON dpu.ID_PESAN_ULANG = pu.ID_PESAN_ULANG
            JOIN pelanggan p ON p.ID_PELANGGAN = pu.ID_PELANGGAN
            JOIN pengguna pg ON pg.ID_PENGGUNA = pu.ID_PENGGUNA
            JOIN barang b ON b.ID_BARANG = dpu.ID_BARANG
            WHERE pu.ID_PENGGUNA = '$id' AND pu.TGL_PESAN_ULANG BETWEEN '$tgaw' AND '$tgak'
                "
            )->result();
        }else{
            $pesanulang = $this->db->query(
                "
               SELECT * FROM pesan_ulang pu
            JOIN detail_pesan_ulang dpu ON dpu.ID_PESAN_ULANG = pu.ID_PESAN_ULANG
            JOIN pelanggan p ON p.ID_PELANGGAN = pu.ID_PELANGGAN
            JOIN pengguna pg ON pg.ID_PENGGUNA = pu.ID_PENGGUNA
            JOIN barang b ON b.ID_BARANG = dpu.ID_BARANG
            WHERE pu.TGL_PESAN_ULANG BETWEEN '$tgaw' AND '$tgak'
                "
            )->result();
        }

        /*$data['pesanulang2'] = $this->db->query(

            "

            SELECT * FROM pesan_ulang pu

            JOIN detail_pesan_ulang dpu ON dpu.ID_PESAN_ULANG = pu.ID_PESAN_ULANG

            JOIN pelanggan p ON p.ID_PELANGGAN = pu.ID_PELANGGAN

            JOIN pengguna pg ON pg.ID_PENGGUNA = pu.ID_PENGGUNA

            JOIN barang b ON b.ID_BARANG = dpu.ID_BARANG

            WHERE pu.TGL_PESAN_ULANG BETWEEN '$tgaw' AND '$tgak'

            "

        )->result();

        $data['user'] = $this->db->get_where('pengguna', ['EMAIL_PENGGUNA' => $this->session->userdata('email')])->row();

        $data['pesanulang'] = $this->db->query(

            "

                SELECT * FROM pesan_ulang pu

                JOIN detail_pesan_ulang dpu ON dpu.ID_PESAN_ULANG = pu.ID_PESAN_ULANG

                JOIN barang b ON b.ID_BARANG = dpu.ID_BARANG

                JOIN pelanggan pl ON pl.ID_PELANGGAN = pu.ID_PELANGGAN

                "

        )->row();*/

        

        //$tgls = date("Y-m-d");

        error_reporting(0); // AGAR ERROR MASALAH VERSI PHP TIDAK MUNCUL



        $pdf = new FPDF('P', 'mm', 'A4');

        $pdf->AddPage();



         $pdf->SetFont('Times', 'B', 16);

        $pdf->Cell(0, 7, 'MUTIARA CEMERLANG TEKNOLOGI', 0, 1, 'C');

        $pdf->SetFont('Times', '', 10);

        $pdf->Cell(0, 7, 'Alamat : Jl. Raya Wates No.3, Kec. Tanggulangin, Kabupaten Sidoarjo', 0, 1, 'C');

        $pdf->Cell(0, 7, 'Email : info@mutiaract.com, Telp/HP : 082328382002', 0, 1, 'C');

        $pdf->Cell(0, 1, '___________________________________________________________________________________________________', 0, 1, 'C');

        $pdf->SetFont('Times', '', 7);

        $pdf->Cell(0, 1, '_____________________________________________________________________________________________________________________________________________', 0, 1, 'C');

        $pdf->Ln(8);

        

        $pdf->SetFont('Times', 'B', 14);

        $pdf->Cell(0, 7, 'LAPORAN PESAN ULANG', 0, 1, 'C');

        $pdf->Cell(20, 7, '', 0, 1);



        //$pdf->Ln(20);

        $pdf->SetFont('Times', 'B', 10);

        $pdf->Cell(15, 6, 'Periode', 0, 0, 'L');

        $pdf->Cell(5, 6, ':', 0, 0, 'L');

        $pdf->Cell(30, 6, date_indo($tgaw).' - '.date_indo($tgak), 0, 1, 'L');



        $pdf->Ln(3);



        $pdf->SetFont('Times', 'B', 10);

        $pdf->Cell(10, 6, 'No', 1, 0, 'C');

        $pdf->Cell(35, 6, 'Nama Sales', 1, 0, 'C');

        $pdf->Cell(35, 6, 'Nama Pelanggan', 1, 0, 'C');

        $pdf->Cell(35, 6, 'Nama Barang', 1, 0, 'C');

        $pdf->Cell(15, 6, 'Jumlah', 1, 0, 'C');

        $pdf->Cell(30, 6, 'Pembayaran', 1, 0, 'C');

        $pdf->Cell(30, 6, 'Sub Total', 1, 1, 'C');



        $pdf->SetFont('Times', '', 10);

        $no = 0;

        $ttl = 0;

        foreach ($pesanulang as $pnj2) {

            $no++;

            $sub = $pnj2->HARGA_JUAL_BARANG * $pnj2->JUMLAH_PESAN_ULANG;

            $pdf->Cell(10, 6, $no, 1, 0, 'C');

            $pdf->Cell(35, 6, $pnj2->NAMA_PENGGUNA, 1, 0);

            $pdf->Cell(35, 6, $pnj2->NAMA_PELANGGAN, 1, 0);

            $pdf->Cell(35, 6, $pnj2->NAMA_BARANG, 1, 0);

            $pdf->Cell(15, 6, $pnj2->JUMLAH_PESAN_ULANG, 1, 0,'C');

            $pdf->Cell(30, 6, $pnj2->STATUS_PEMBAYARAN_PESAN_ULANG, 1, 0,'C');

            $pdf->Cell(30, 6, 'Rp. '.number_format($sub), 1, 1,'L');

            $ttl = $ttl + $sub;

        }

        $pdf->SetFont('Times', 'B', 10);

        $pdf->Cell(160, 6, 'Total ', 1, 0,'C');

        $pdf->Cell(30, 6, 'Rp. '.number_format($ttl), 1, 1,'L');



        $pdf->SetY(-65);

        $pdf->SetFont('Times', '', 10);

        $pdf->line($pdf->GetX(), $pdf->GetY(), $pdf->GetX(), $pdf->GetY());

        $pdf->SetY(-65);

        $pdf->SetX(0);

        $pdf->Ln(1);



        $pdf->Cell(140, 6, '', 0, 0, 'C');

        $pdf->Cell(40, 6, 'Sidoarjo, ' . date_indo($tgl), 0, 1, 'C');



        $pdf->SetY(-55);

        $pdf->SetFont('Times', '', 10);

        $pdf->line($pdf->GetX(), $pdf->GetY(), $pdf->GetX(), $pdf->GetY());

        $pdf->SetY(-55);

        $pdf->SetX(0);

        $pdf->Ln(1);



        $pdf->Cell(140, 6, '', 0, 0, 'C');

        $pdf->Cell(40, 6, 'Yang bertanda tangan', 0, 1, 'C');



        $pdf->SetY(-30);

        $pdf->SetFont('Times', '', 8);

        $pdf->line($pdf->GetX(), $pdf->GetY(), $pdf->GetX(), $pdf->GetY());

        $pdf->SetY(-30);

        $pdf->SetX(0);

        $pdf->Ln(1);



       

        $pdf->Cell(140, 6, '', 0, 0, 'C');

        $pdf->SetFont('Times', '', 10);

        $pdf->Cell(40, 6, '('.$user->NAMA_PENGGUNA.')', 0, 1, 'C');



        $pdf->Output();

    }

}



/* End of file Supplier_c.php */

