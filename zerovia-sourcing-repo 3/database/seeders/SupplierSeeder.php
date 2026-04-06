<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            // ── Switzerland ────────────────────────────────────────────────
            ['name'=>'Geberit AG','city'=>'Rapperswil-Jona','country'=>'CH','lat'=>47.2260,'lng'=>8.8193,'esg_score'=>88,'risk_level'=>'low','noga_codes'=>['C22','C23'],'certifications'=>['ISO 9001','ISO 14001','ISO 50001','EcoVadis Gold'],'website'=>'geberit.com','email'=>'procurement@geberit.com'],
            ['name'=>'SFS Group AG','city'=>'Heerbrugg','country'=>'CH','lat'=>47.4042,'lng'=>9.6323,'esg_score'=>82,'risk_level'=>'low','noga_codes'=>['C25','C28'],'certifications'=>['ISO 9001','ISO 14001','UN Global Compact'],'website'=>'sfs.com','email'=>'procurement@sfs.com'],
            ['name'=>'Bühler AG','city'=>'Uzwil','country'=>'CH','lat'=>47.4373,'lng'=>9.1333,'esg_score'=>79,'risk_level'=>'low','noga_codes'=>['C28','C29'],'certifications'=>['ISO 9001','ISO 14001','ISO 45001'],'website'=>'buhlergroup.com','email'=>'sourcing@buhlergroup.com'],
            ['name'=>'Vetropack Holding AG','city'=>'Bülach','country'=>'CH','lat'=>47.5228,'lng'=>8.5389,'esg_score'=>75,'risk_level'=>'low','noga_codes'=>['C23'],'certifications'=>['ISO 9001','ISO 14001','REACH'],'website'=>'vetropack.com','email'=>'procurement@vetropack.com'],
            ['name'=>'Glatfelter Switzerland','city'=>'Gernsbach','country'=>'CH','lat'=>47.3200,'lng'=>8.0500,'esg_score'=>71,'risk_level'=>'medium','noga_codes'=>['C17','C17.12'],'certifications'=>['ISO 9001','ISO 14001','FSC','PEFC'],'website'=>'glatfelter.com','email'=>'einkauf@glatfelter.com'],
            ['name'=>'Perlen Papier AG','city'=>'Root','country'=>'CH','lat'=>47.1089,'lng'=>8.3836,'esg_score'=>69,'risk_level'=>'medium','noga_codes'=>['C17','C17.21'],'certifications'=>['ISO 9001','ISO 14001','FSC'],'website'=>'perlen.com','email'=>'procurement@perlen.com'],
            ['name'=>'Schweizer Electronic AG','city'=>'Schramberg','country'=>'CH','lat'=>48.2232,'lng'=>8.3889,'esg_score'=>74,'risk_level'=>'low','noga_codes'=>['C26','C26.1'],'certifications'=>['ISO 9001','ISO 14001','CE','REACH','RoHS'],'website'=>'se.com','email'=>'einkauf@se.com'],
            ['name'=>'BACHEM AG','city'=>'Bubendorf','country'=>'CH','lat'=>47.4500,'lng'=>7.7333,'esg_score'=>85,'risk_level'=>'low','noga_codes'=>['C20','C21'],'certifications'=>['ISO 9001','ISO 14001','UN Global Compact','EcoVadis Silver'],'website'=>'bachem.com','email'=>'procurement@bachem.com'],

            // ── Germany ─────────────────────────────────────────────────────
            ['name'=>'Hansgrohe SE','city'=>'Schiltach','country'=>'DE','lat'=>48.2847,'lng'=>8.3417,'esg_score'=>83,'risk_level'=>'low','noga_codes'=>['C28','C28.1'],'certifications'=>['ISO 9001','ISO 14001','ISO 50001','UN Global Compact'],'website'=>'hansgrohe.com','email'=>'procurement@hansgrohe.com'],
            ['name'=>'Schüco International KG','city'=>'Bielefeld','country'=>'DE','lat'=>52.0302,'lng'=>8.5325,'esg_score'=>76,'risk_level'=>'low','noga_codes'=>['C25','C25.1'],'certifications'=>['ISO 9001','ISO 14001','CE'],'website'=>'schueco.com','email'=>'einkauf@schueco.com'],
            ['name'=>'Metsä Board Deutschland','city'=>'Düsseldorf','country'=>'DE','lat'=>51.2217,'lng'=>6.7762,'esg_score'=>81,'risk_level'=>'low','noga_codes'=>['C17','C17.21','C17.22'],'certifications'=>['ISO 9001','ISO 14001','FSC','PEFC','EcoVadis Gold'],'website'=>'metsaboard.com','email'=>'procurement@metsaboard.com'],
            ['name'=>'Klöckner & Co SE','city'=>'Duisburg','country'=>'DE','lat'=>51.4344,'lng'=>6.7623,'esg_score'=>67,'risk_level'=>'medium','noga_codes'=>['G46','G46.7'],'certifications'=>['ISO 9001','ISO 14001'],'website'=>'kloeckner.com','email'=>'einkauf@kloeckner.com'],
            ['name'=>'Trumpf GmbH + Co. KG','city'=>'Ditzingen','country'=>'DE','lat'=>48.8281,'lng'=>9.0664,'esg_score'=>86,'risk_level'=>'low','noga_codes'=>['C28','C28.4'],'certifications'=>['ISO 9001','ISO 14001','UN Global Compact','EcoVadis Silver'],'website'=>'trumpf.com','email'=>'procurement@trumpf.com'],
            ['name'=>'Sto SE & Co. KGaA','city'=>'Stühlingen','country'=>'DE','lat'=>47.7342,'lng'=>8.4458,'esg_score'=>72,'risk_level'=>'low','noga_codes'=>['C20','C20.3'],'certifications'=>['ISO 9001','ISO 14001','EcoVadis Silver'],'website'=>'sto.com','email'=>'sourcing@sto.com'],
            ['name'=>'Dachser GmbH & Co. KG','city'=>'Kempten','country'=>'DE','lat'=>47.7228,'lng'=>10.3172,'esg_score'=>78,'risk_level'=>'low','noga_codes'=>['H49','H52'],'certifications'=>['ISO 9001','ISO 14001','ISO 45001'],'website'=>'dachser.com','email'=>'procurement@dachser.com'],
            ['name'=>'Mahr GmbH','city'=>'Göttingen','country'=>'DE','lat'=>51.5328,'lng'=>9.9354,'esg_score'=>70,'risk_level'=>'medium','noga_codes'=>['C26','C26.5'],'certifications'=>['ISO 9001','CE','REACH'],'website'=>'mahr.com','email'=>'einkauf@mahr.com'],

            // ── Austria ─────────────────────────────────────────────────────
            ['name'=>'Engel Austria GmbH','city'=>'Schwertberg','country'=>'AT','lat'=>48.2667,'lng'=>14.5667,'esg_score'=>77,'risk_level'=>'low','noga_codes'=>['C28','C28.9'],'certifications'=>['ISO 9001','ISO 14001','CE'],'website'=>'engelglobal.com','email'=>'procurement@engelglobal.com'],
            ['name'=>'Mondi AG','city'=>'Wien','country'=>'AT','lat'=>48.2082,'lng'=>16.3738,'esg_score'=>87,'risk_level'=>'low','noga_codes'=>['C17','C17.21','C17.22'],'certifications'=>['ISO 9001','ISO 14001','FSC','PEFC','EcoVadis Gold','UN Global Compact'],'website'=>'mondigroup.com','email'=>'procurement@mondigroup.com'],
            ['name'=>'Anton Paar GmbH','city'=>'Graz','country'=>'AT','lat'=>47.0707,'lng'=>15.4395,'esg_score'=>80,'risk_level'=>'low','noga_codes'=>['C26','M71'],'certifications'=>['ISO 9001','ISO 14001','CE','REACH'],'website'=>'anton-paar.com','email'=>'sourcing@anton-paar.com'],

            // ── France ──────────────────────────────────────────────────────
            ['name'=>'Smurfit Kappa France','city'=>'Lyon','country'=>'FR','lat'=>45.7640,'lng'=>4.8357,'esg_score'=>79,'risk_level'=>'low','noga_codes'=>['C17','C17.21'],'certifications'=>['ISO 9001','ISO 14001','FSC','PEFC','EcoVadis Silver'],'website'=>'smurfitkappa.com','email'=>'procurement@smurfitkappa.com'],
            ['name'=>'Somfy Group','city'=>'Cluses','country'=>'FR','lat'=>46.0617,'lng'=>6.5814,'esg_score'=>76,'risk_level'=>'low','noga_codes'=>['C27','C27.5'],'certifications'=>['ISO 9001','ISO 14001','CE','RoHS'],'website'=>'somfy.com','email'=>'achats@somfy.com'],

            // ── Netherlands ─────────────────────────────────────────────────
            ['name'=>'DSM-Firmenich AG','city'=>'Kaiseraugst','country'=>'CH','lat'=>47.5344,'lng'=>7.7614,'esg_score'=>91,'risk_level'=>'low','noga_codes'=>['C20','C21'],'certifications'=>['ISO 9001','ISO 14001','ISO 50001','EcoVadis Platinum','UN Global Compact'],'website'=>'dsm-firmenich.com','email'=>'procurement@dsm-firmenich.com'],

            // ── Additional CH suppliers ──────────────────────────────────────
            ['name'=>'Zehnder Group AG','city'=>'Gränichen','country'=>'CH','lat'=>47.3589,'lng'=>8.1100,'esg_score'=>73,'risk_level'=>'low','noga_codes'=>['C28','C25'],'certifications'=>['ISO 9001','ISO 14001'],'website'=>'zehnder-group.com','email'=>'einkauf@zehnder-group.com'],
            ['name'=>'Kuoni & Huguenin AG','city'=>'Männedorf','country'=>'CH','lat'=>47.2574,'lng'=>8.6942,'esg_score'=>64,'risk_level'=>'medium','noga_codes'=>['C22','C25'],'certifications'=>['ISO 9001','CE'],'website'=>'kuoni-huguenin.ch','email'=>'procurement@kuoni-huguenin.ch'],
            ['name'=>'Lista Holding AG','city'=>'Erlen','country'=>'CH','lat'=>47.5422,'lng'=>9.0592,'esg_score'=>68,'risk_level'=>'medium','noga_codes'=>['C31','C32'],'certifications'=>['ISO 9001','ISO 14001'],'website'=>'lista.ch','email'=>'einkauf@lista.ch'],
            ['name'=>'Komax Holding AG','city'=>'Dierikon','country'=>'CH','lat'=>47.0742,'lng'=>8.3533,'esg_score'=>76,'risk_level'=>'low','noga_codes'=>['C28','C28.4'],'certifications'=>['ISO 9001','ISO 14001','CE'],'website'=>'komax.com','email'=>'procurement@komax.com'],
        ];

        foreach ($suppliers as $data) {
            Supplier::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        // Generate 50 additional random suppliers via factory
        if (Supplier::count() < 50) {
            Supplier::factory()
                ->count(50 - Supplier::count())
                ->create();
        }

        $this->command->info('✓ ' . Supplier::count() . ' Lieferanten geladen');
    }
}
