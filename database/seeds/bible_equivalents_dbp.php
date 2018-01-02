<?php

use Illuminate\Database\Seeder;
use database\seeds\SeederHelper;
use App\Models\Bible\BibleEquivalent;
class bible_equivalents_dbp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		BibleEquivalent::where('site','bible.is')->delete();
	    $sheet_id = '1pEYc-iYGRdkPpCuzKf4x8AgYJfK4rbTCcrHfRD7TsW4';
	    $gid = '1765248815';

        $seederHelper = new SeederHelper();
        $bibleEquivalents = $seederHelper->csv_to_array('https://docs.google.com/spreadsheets/d/'.$sheet_id.'/export?format=csv&id='.$sheet_id.'&gid='.$gid);
        $seederHelper->seedBibleEquivalents($bibleEquivalents,'faith-comes-by-hearing','api','bible.is');

		// Bible Equivalent
	    $bibles = ['ACAWBTP2ET','ACRAEMP1DA','ACRAEMP2DA','ADYIBTP1DA','ADYIBTP2DA','ADZWBTP2ET','AECEBSP2DA','AEYTBLN2ET','AEYTBLP2ET','AFRNVVN2DA','AFRNVVP2DA','AGUAEMP1DA','AGUAEMP2DA','AGXIBTP2DA','AHLABTP2DA','APSWBTP2DA','ARMANCP1DA','ARPPD1P2ET','ATAPNGN2ET','ATAPNGP2ET','AWXWBTP2ET','AYONTMN2DA','AYONTMN2ET','AYONTMP1DA','AYONTMP1ET','AYONTMP2ET','AZAATTP2DA','AZBEMVN1DA','AZBEMVN1ET','AZBEMVO1ET','AZBEMVP1DA','AZEBSAN2DA','AZEBSAN2ET','AZEBSAO2ET','AZEBSAP2DA','AZEBSAP2ET','BBBWBTN2ET','BBBWBTP2ET','BC1WBTP1DA','BC1WBTP1ET','BC1WBTP2DA','BC1WBTP2ET','BCCWBTP1DA','BCCWBTP1ET','BCCWBTP2DA','BCCWBTP2ET','BCHWBTN2ET','BCHWBTP2ET','BCOWBTP2ET','BDGWYIP1DA','BDGWYIP1ET','BDGWYIP2DA','BDGWYIP2ET','BELJFCN2ET','BELJFCP2ET','BFYWINP1DA','BFYWINP2DA','BGQWINP2DA','BGQWINP2ET','BMKWBTP2ET','BNOWBTN1DA','BNOWBTN2DA','BNOWBTN2ET','BNOWBTP1DA','BNOWBTP1ET','BNOWBTP2DA','BNOWBTP2ET','BNOWYIP1DA','BNOWYIP2DA','BNPWBTN2ET','BNPWBTP2ET','BQ1IBVP1DA','BQ1IBVP1ET','BQ1IBVP2DA','BQ1IBVP2ET','BQJATBN1DA','BQJATBN1ET','BQJATBN2DA','BQJATBN2ET','BQJATBP1ET','BQJATBP2ET','BQPSIMN1DA','BQPSIMN1ET','BQPSIMN2DA','BQPSIMN2ET','BQPSIMP1ET','BQPSIMP2ET','BSPPBTP2ET','BUSSIMN1DA','BUSSIMN1ET','BUSSIMN2ET','BUSSIMP1ET','BUSSIMP2ET','BYRWBTN2ET','BYRWBTP2ET','BYTWBTP2ET','BYXBTAN2ET','BYXBTAP2ET','BZHPNGN1ET','BZHPNGN2ET','BZHPNGP1DA','BZHPNGP2DA','BZIWYIP1DA','BZIWYIP2DA','CA2NTMP1DA','CASNTMN1DA','CASNTMN1ET','CASNTMP1DA','CASNTMP1ET','CBITBLN1DA','CBITBLN1ET','CBITBLN2ET','CBITBLP1ET','CBITBLP2ET','CBTTBLN2DA','CBTTBLN2ET','CHEIBTN1DA','CHEIBTN1ET','CHEIBTN2DA','CHEIBTN2ET','CHEIBTO1ET','CHEIBTO2ET','CHEIBTP2DA','CHEIBTP2ET','CHFTBLN1ET','CHFTBLN2ET','CHFTBLP1DA','CHNUN1N2DA','CHNUN1N2ET','CHNUN1O2ET','CHNUN1P2DA','CHNUNVN1ET','CHNUNVN2DA','CHNUNVN2ET','CHNUNVO1ET','CHNUNVO2ET','CHNUNVP1DA','CHNUNVP2DA','CJECWKP1DA','CJSIBTP2DA','CKTIBTP1DA','CKTIBTP1ET','CRKCBSP1DA','CRONABP1DA','CTOWYIP2DA','CTOWYIP2ET','DBJWBTP1DA','DBJWBTP2DA','DEDWBTN1DA','DEDWBTN1ET','DEDWBTN2ET','DEDWBTP1ET','DEDWBTP2ET','DGRBSCN1ET','DGRBSCP1DA','EMIWBTP2ET','ENAPBTP2DA','ENGIMEP1DA','ENGIMEP1ET','ENGIMEP2DA','ENGIMEP2ET','ENGNABN2DA','ENGNABN2ET','ESKWBTN2ET','ESKWBTP2DA','ETRWBTP2ET','EVNIBTP1DA','EVNIBTP1ET','FMUWINP1DA','FMUWINP2DA','GAMWBTP2ET','GBOLBTN1ET','GBOLBTN2ET','GBOLBTP1DA','GBOLBTP2DA','GEBFBKP2DA','GEBFBKP2ET','GERNEUN2ET','GERNEUO2ET','GWJNVSP2DA','HATBIVN2ET','HATBIVP2ET','HBRHMTC2DA','HBRHMTN1DA','HBRHMTN1ET','HBRHMTN2DA','HBRHMTN2ET','HBRHMTO2DA','HBRHMTO2ET','HBRHMTP2DA','HBRHMTP2ET','HOPABSP2DA','IC1WBTP1DA','INZSHLN2DA','INZSHLN2ET','INZSHLO2ET','INZSHLP2DA','IOUWBTN1DA','IOUWBTN1ET','IOUWBTN2ET','IOUWBTP1DA','IOUWBTP1ET','IOUWBTP2ET','ITLIBTP1DA','JACAEMP1DA','JACAEMP2DA','JAEPNGN2ET','JAEPNGP2ET','JITWYIP2DA','KAPIBTP1DA','KAZKAZN2DA','KAZKAZN2ET','KAZKAZO2ET','KAZKAZP2DA','KCAIBTP1DA','KEWPNGN2ET','KEWPNGP2ET','KFPWINP2ET','KGFGBTN2ET','KGFGBTP2ET','KJGWYIP1DA','KJHARVN1ET','KJHARVP1DA','KKCWBTP2ET','KMGPNGP2ET','KMRIBTP1DA','KNJAEMN1ET','KNJAEMN2ET','KNJAEMO1ET','KNJAEMO2ET','KNJAEMP1DA','KNJAEMP2DA','KPXTBLN2ET','KPYIBTP1DA','KQCWBTP2ET','KQFWBTP2ET','KQWWBTP2ET','KSZWINP2DA','KSZWINP2ET','KUMIBTN2DA','KUMIBTN2ET','KUMIBTP1DA','KUMIBTP2DA','KYGTBLN2ET','KYGTBLP2ET','LATLVRN1DA','LATLVRP1DA','LBBWBTP2ET','LBEIBTP1DA','LCMPNGN2ET','LCMPNGP2ET','LIDWBTN2ET','LIDWBTP2ET','LMEABTN2DA','LMEABTN2ET','LMEABTP2ET','LOQWBTP2ET','MAJBTTP1DA','MAJTBLN1DA','MAJTBLN1ET','MAJTBLN2DA','MAJTBLN2ET','MAJTBLP1ET','MAJTBLP2ET','MAQTBLN1ET','MAQTBLP1DA','MBKWBTP2DA','MBZTBLP1DA','MBZTPLP1ET','MCBTBLN2DA','MCBTBLN2ET','MCBTBLP2ET','MCRPNGP2DA','MEDPNGN1ET','MEDPNGN2ET','MEDPNGP1DA','MEDPNGP2DA','MEHTBLP1DA','MGCPBTP2ET','MHMBSBN2DA','MHMBSBN2ET','MHMBSBP1DA','MHMBSBP2DA','MHUWINP1DA','MKSTBLN1ET','MKSTBLP1DA','MKWIBSP1DA','MMXWBTN2ET','MMXWBTP2ET','MNAPNGP2ET','MPMBTTN1ET','MPMBTTP1DA','MPPWBTP2ET','MPXPNGN1ET','MPXPNGN2DA','MPXPNGN2ET','MPXPNGP1DA','MPXPNGP1ET','MPXPNGP2ET','MSCPBTP2ET','MVCAEMP1DA','MVCAEMP2DA','MWCWBTP2ET','MWWHDVN2DA','MWWHDVN2ET','MWWHDVO2DA','MWWHDVP2DA','MZZWBTP2ET','NASPNGN2DA','NASPNGN2ET','NASPNGP2ET','NBQNTTP2ET','NCLTBLP1DA','NCLTBLP1ET','NEWNCLN1DA','NEWNCLN1ET','NEWNCLN2DA','NEWNCLN2ET','NEWNCLP1ET','NEWNCLP2ET','NGUOTSP1DA','NIFWBTP2ET','NKOWBTN1DA','NKOWBTN1ET','NKOWBTN2DA','NKOWBTN2ET','NKOWBTP1DA','NKOWBTP1ET','NKOWBTP2DA','NKOWBTP2ET','NOPWBTN2ET','NOPWBTP2ET','NSKNDCN1DA','NSKNDCP1DA','NSSWBTP2ET','NSUWBTP1DA','NTMWBTN2ET','NTMWBTP2ET','NUZTBLP1DA','NUZTBLP1ET','OJSCBSN2DA','OJSCBSP2DA','OM1TBLP1DA','OM1TBLP1ET','OM1TBLP2DA','OM1TBLP2ET','OMWATVP2ET','ON1ROVP2DA','ON2GOVP2DA','ONBNVSP1DA','ONBNVSP1ET','ONBNVSP2DA','ONBNVSP2ET','ONRWOVP2DA','OREWBTP1DA','OREWBTP2DA','OSSODVP1DA','OSSODVP2DA','PJTBSAN2ET','PJTBSAP2ET','POBAEMP1DA','POBAEMP2DA','POETBLN1ET','POETBLP1DA','POETBLP1ET','POEWYIN1DA','POEWYIN1ET','POEWYIN2DA','POEWYIN2ET','POEWYIP1DA','POEWYIP1ET','POLNCVN2DA','POLNCVN2ET','POLNCVP1DA','POWWBTP1DA','PTULAIN2DA','PTULAIP2ET','QUCAEMP1DA','QUCAEMP2DA','RA1TBLP2DA','RONDCVN1DA','RONDCVP1DA','SACWBTP2DA','SAHIBTP1DA','SAHIBTP1ET','SAHIBTP2DA','SAHIBTP2ET','SBEWBTP2ET','SEAWYIP1DA','SEAWYIP2DA','SEITBLP1DA','SHIRBDN1DA','SHIRBDN1ET','SHIRBDP1DA','SHOSIMP2ET','SHPTBLN2DA','SHPTBLN2ET','SHPTBLP2ET','SPNLARP2DA','SPYWBTN2DA','SPYWBTN2ET','SPYWBTP2ET','SSDWBTN2ET','SSDWBTP2ET','SSOWBTP2DA','SSXPNGN2ET','SSXPNGP2ET','STOWBTP1DA','SWDIBSP1DA','SWPPNGN2ET','SWPPNGP2ET','TBGTBLN2ET','TBGTBLP2ET','TBTPTBP1DA','TBTPTBP1ET','TBTPTBP2DA','TGBTBLP2ET','THATSVP2DA','THATSVP2ET','TIXWBTP1DA','TKRIBTP1DA','TNCWBTP2ET','TODPBTP2DA','TPAWBTP2ET','TR1TBLP1DA','TR1TBLP1ET','TRCTBLP1DA','TRNNTMN1DA','TRNNTMN1ET','TRNNTMP1DA','TU2WBTP2ET','TUCOOVN2ET','TUCOOVP2ET','TUKIBTN1ET','TUKIBTN2ET','TUKIBTO1ET','TUKIBTO2ET','TUKIBTP1DA','TUKIBTP1ET','TUKIBTP2DA','TUKIBTP2ET','TXATBLN1DA','TXATBLN1ET','TXATBLN2DA','TXATBLN2ET','TXATBLO1ET','TXATBLO2ET','TXATBLP1DA','TXATBLP1ET','TXATBLP2DA','TXATBLP2ET','TYESIMP1DA','TYESIMP1ET','TYESIMP2DA','TYESIMP2ET','TYVIBTN2DA','TYVIBTN2ET','TYVIBTO1ET','TYVIBTO2ET','TYVIBTP1DA','TYVIBTP2DA','UBRTBLN2ET','UBRTBLP2ET','UI1UMKN1DA','UI1UMKN1ET','UI1UMKP1DA','UI1UMKP1ET','UI1UMKP2ET','UIGUMKN1DA','UIGUMKN1ET','UIGUMKP1DA','UIGUMKP2ET','URWPBTP2ET','UZBIBTN2DA','UZBIBTN2ET','UZBIBTP2DA','UZBNVSP2DA','WEDWBTP2ET','WEDWTDP2ET','WNCPNGN2ET','WNCPNGP2ET','WOSWBTN2ET','WOSWBTP1ET','WOSWBTP2ET','WR1WSVP2DA','WR2WBVP2DA','WR3WPVP2DA','WRAWRVP2DA','WRSPNGN2ET','WRSPNGP2ET','WSKPNGN2ET','WSKPNGP2ET','XALIBTN1ET','XALIBTN2DA','XALIBTN2ET','XALIBTP1DA','XALIBTP1ET','XB1KYVP2ET','XBIKWVP2ET','XLAPNGN2ET','XSIWBTN2ET','XSIWBTP2ET','XTATBLP1DA','XTMTBLN1DA','XTMTBLN1ET','XTMTBLN2DA','XTMTBLN2ET','XTMTBLP1ET','XTMTBLP2ET','YAQTBLN1ET','YAQTBLP1DA','YCLNVSN1DA','YCLNVSN2DA','YCLNVSP1DA','YCLNVSP2DA','YCLWBTP1DA','YCLWBTP1ET','YCLWBTP2DA','YCLWBTP2ET','YDDLMMP1DA','YONPNGN2ET','YONPNGP2ET','YRKIBTP2DA','YRKIBTP2ET','YUZNTMN1DA','YUZNTMN1ET','YUZNTMP1DA','ZABBTTN1ET','ZABBTTP1DA','ZACSIMN1ET','ZACSIMP1DA','ZADTBLN2DA','ZADTBLN2ET','ZAKWYIP1DA','ZAKWYIP2DA','ZAMILMN1ET','ZAMILMP1DA','ZAQTBLP1DA','ZAQTBLP1ET','ZAVTBLP1DA','ZAVTBLP1ET','ZAWSIMP1DA','ZPMSIMN1ET','ZPMSIMP1DA','ZPQBTTN1ET','ZPQBTTP1DA','ZPVTBLP1DA','ZYBNBPN1DA','ZYBNBPN1ET','ZYBNBPP1DA','ZYBNBPP1ET'];
	    $bibleEquivalents = BibleEquivalent::where('site','bible.is')->get();
	    foreach($bibleEquivalents as $bible_equivalent) {
		    foreach ($bibles as $bible) {
			    if(strpos($bible, $bible_equivalent->equivalent_id) !== false) {
				    BibleEquivalent::create([
					    'bible_id'          => $bible_equivalent->bible_id,
					    'equivalent_id'     => $bible,
					    'organization_id'   => 9,
					    'type'              => 'api',
					    'site'              => 'bible.is',
					    'suffix'            => '',
				    ]);
			    }
		    }
	    }

    }
}