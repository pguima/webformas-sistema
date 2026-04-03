<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Web;
use Illuminate\Database\Seeder;

class ClientsFromCsvSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->data() as [$clientName, $domain, $platformRaw]) {
            $client = Client::firstOrCreate(['name' => $clientName]);

            if (empty($domain)) {
                continue;
            }

            $isExternal = str_contains(strtolower((string) $platformRaw), 'nosso');
            $url        = rtrim(trim($domain), '/');

            Web::firstOrCreate(
                ['client_id' => $client->id, 'url' => $url],
                [
                    'name'     => $clientName,
                    'platform' => $isExternal ? null : $this->normalizePlatform($platformRaw),
                    'notes'    => $isExternal ? 'Site externo, não gerenciado pela agência.' : null,
                    'status'   => 'active',
                ]
            );
        }
    }

    private function normalizePlatform(?string $p): ?string
    {
        if (!$p) {
            return null;
        }
        $lower = strtolower(trim($p));
        if (str_contains($lower, 'wordpress')) {
            return 'WordPress';
        }
        if ($lower === 'html') {
            return 'HTML';
        }
        return null;
    }

    /** @return array<int, array{string, string, string}> */
    private function data(): array
    {
        return [
            ['3D PICTURES',                                               '3dpictures.com.br',                    'HTML'],
            ['A CAPELA',                                                  'capelamoveisantigos.com.br',            'HTML'],
            ['ABC REDES E TELAS',                                         'abcredesetelas.com.br',                 'WordPress'],
            ['ABRIU PORTAS AUTOMÁTICAS',                                  'abriuportasautomaticas.com.br',         'WordPress'],
            ['ACL TUBOS TREFILADOS',                                      '',                                      ''],
            ['AD LUCRAM',                                                 'grupoadlucram.com.br',                  'HTML'],
            ['AFINDER',                                                   '',                                      ''],
            ['ÁGUA MAR DEL PLATA',                                        'aguamardelplata.com.br',                'WordPress'],
            ['ALEAN DIVISÓRIAS E PORTAS SANITÁRIAS',                      'aleandivisoriassanitarias.com.br',      'HTML'],
            ['ALEXANDRE FERREIRA ADVOGADOS',                              'alexandreferreiraadv.com.br',           'HTML'],
            ['ALNITUR TRANSPORTES',                                       'alnitur.com.br',                        'HTML'],
            ['ALPHA REDES E TELAS',                                       'alpharedesetelas.com.br',               'WordPress'],
            ['ALTERNATIVA DOS AZULEJOS',                                  'alternativadosazulejos.com.br',         'HTML'],
            ['AMBIKONTROL INDÚSTRIA E COMÉRCIO LTDA',                     'ambikontrol.com.br',                    'HTML'],
            ['AMÉRICA CERTIFICADORA',                                     'americacertificadora.com',              'WordPress'],
            ['ANIMAL LIFE',                                               'animalife.com.br',                      'HTML'],
            ['AQUECEDORES CARINAS',                                       'aquecedorescarinas.com.br',             'HTML'],
            ['AQUECEDORES IMPÉRIO',                                       'aquecedoresimperio.com.br',             'WordPress'],
            ['ARCO DECOR',                                                'arcodecor.com.br',                      'HTML'],
            ['ART FLEX DIVISÓRIAS',                                       'divisoriasartflex.com.br',              'HTML'],
            ['ARTE FUTURA BRASIL',                                        '',                                      ''],
            ['ARTE SÃO PAULO MOEMA',                                      'artesaopaulo.com.br',                   'WordPress'],
            ['ASLAM CORRETORA',                                           'aslamseguros.com.br',                   'HTML'],
            ['ASSAHI MÁQUINAS',                                           'assahimaquinas.com.br',                 'WordPress'],
            ['ASSESCO ASSESSORIA CONTÁBIL',                               'assescocontabil.com.br',                'HTML'],
            ['ATG EQUIPAMENTOS PARA EMBALAGENS',                          'atggrampeadores.com.br',                'HTML'],
            ['ATITUDE CURSOS E TREINAMENTO',                              'atitudetreinamento.com.br',             'HTML'],
            ['BALA AUTO CENTER',                                          'balaautocenter.com.br',                 'HTML'],
            ['BASTOS CONGELADOS',                                         '',                                      ''],
            ['BORBA GATO AZULEJOS E PISOS ANTIGOS',                       'azulejospisosantigos.com.br',           'HTML'],
            ['BOSSANOVA DECOR',                                           '',                                      'HTML'],
            ['BOTTO',                                                     'botto.com.br',                          'HTML'],
            ['BRASIL FUSO',                                               'brasilfuso.com.br',                     'HTML'],
            ['BRASIMET PROCESSAMENTO TÉRMICO LTDA',                       'brasimet.com.br',                       'WordPress'],
            ['BUNNY VETS CLÍNICA VETERINÁRIA',                            '',                                      'HTML'],
            ['CAJAMAR COMÉRCIO DE BOMBONAS',                              'cajamarbombonas.com.br',                'HTML'],
            ['CAMARGO E HEIDERICH ADVOGADOS',                             'cahe.adv.br',                           'HTML'],
            ['CAMP PORTARIA E SERVIÇOS',                                  'campportariaeservicos.com.br',           'HTML'],
            ['CARINA AMORIM',                                             'carinaamorim.com',                      'HTML'],
            ['CARLOS FABIO ANTONIAZI',                                    '',                                      ''],
            ['CASA DE CORTINAS',                                          'casadecortinasemsaopaulo.com.br',        'HTML'],
            ['CAVALCANTTI ENGENHARIA',                                    'cavalcanttiengenharia.com.br',           'WordPress'],
            ['CBJ 479 AUTO PEÇAS',                                        'cbjautopecas.com.br',                   'HTML'],
            ['CCONTTUR TRANSPORTES',                                      'cconttur.com.br',                       'HTML'],
            ['CEDO EQUIPAMENTOS DE SEGURANÇA',                            'cedoequipamentos.com.br',               'HTML'],
            ['CEMASOL COMERCIAL ELÉTRICA',                                'eletricacemasol.com.br',                'WordPress'],
            ['CEMITÉRIO DOS AZULEJOS PEDRO LESSA',                        'cemiterioazulejos.com.br',              'HTML'],
            ['CEMITÉRIO DOS PISOS E AZULEJOS ANTIGOS',                    'cemiteriodepisoseazulejos.com.br',       'HTML'],
            ['CIRCO MÁGICO BUFFET INFANTIL',                              'circomagico.com.br',                    'HTML'],
            ['CLEMENTI SERVIÇOS MÉDICOS',                                 'lukedocs.com.br',                       'WordPress'],
            ['CLI LOGÍSTICA',                                             'clilogistica.com.br',                   'HTML'],
            ['CLÍNICA DE OLHOS VEDERE',                                   'clinicadeolhosvedere.com.br',           'HTML'],
            ['CLÍNICA DOUTORA DENISE TROMBINI',                           'clinicadradenisetrombini.com.br',       'HTML'],
            ['COLÉGIO HAYA',                                              'haya.com.br',                           'HTML'],
            ['COLÉGIO SANTA IZILDINHA',                                   'colegiosantaizildinha.com.br',           'WordPress'],
            ['COMERCIAL FORTE',                                           'comercialforte.com.br',                 'HTML'],
            ['CONDOMÍNIO VISTA BELA ESTÂNCIA',                            'vistabelaestancia.com.br',              'WordPress'],
            ['CRIS LOCAÇÃO',                                              'crislocacao.com.br',                    'HTML'],
            ['CRISDART SERRALHERIA',                                      'crisdartestruturametalica.com.br',       'HTML'],
            ['D-AÇO DISTRIBUIDORA E SERVIÇOS DE FERRO E AÇO LTDA',       'd-acocomercial.com.br',                 'HTML'],
            ['DEDETIZADORA DALVER',                                       'dedetizadoradalver.com.br',             'HTML'],
            ['DESPACHANTE ÓTIMO',                                         '',                                      'HTML'],
            ['DRAGÃO PLÁSTICOS',                                          'dragaoplasticos.com.br',                'HTML'],
            ['DREAM NOW',                                                 'dreamnow.com.br',                       'HTML'],
            ['DUMAS DECORAÇÕES',                                          'dumasdecoracoes.com.br',                'HTML'],
            ['EDEN FLORA PAISAGISMO',                                     'edenflorapaisagismo.com.br',            'HTML'],
            ['EDU GELO',                                                  'edugelo.com.br',                        'HTML'],
            ['EIKAL',                                                     'eikal.com.br',                          'WordPress'],
            ['EIKIL',                                                     'eikil.com.br',                          'WordPress'],
            ['EMANUEL LIMA ADVOCACIA',                                    'emanuellima.com.br',                    'HTML'],
            ['ENGTECH LTDA',                                              '',                                      ''],
            ['EQUIPAFLEX',                                                'equipaflexse.com.br',                   'WordPress'],
            ['ESCLA',                                                     'escla.com.br',                          'WordPress'],
            ['EVOLUIU IÇAMENTOS',                                         'evoluiuicamentos.com.br',               'HTML'],
            ['EXÉMIA COMÉRCIO DE BEBIDAS',                                'marvidistribuidora.com.br',             'WordPress'],
            ['EXPANSÃO METAL',                                            'expansaometal.com.br',                  'HTML'],
            ['FÁBRICA DE ÓCULOS S.R',                                     'oticasr.com.br',                        'HTML'],
            ['FACCINA',                                                   'faccinaequipamentos.com.br',            'WordPress'],
            ['FALCON EMPILHADEIRAS',                                      'falconempilhadeiras.com.br',            'HTML'],
            ['FE TELHADOS',                                               'fetelhados.com.br',                     'site não é nosso'],
            ['FERRO E AÇO PROGRESSO',                                     'ferroeacoprogresso.com.br',             'WordPress'],
            ['FGRAN - GRANILITE',                                         'fgrangranilite.com.br',                 'HTML'],
            ['FHAMA CLEAN SERVIÇOS',                                      'fhamacleanservicos.com.br',             'WordPress'],
            ['FINOTTI ALFAIATE',                                          'finottialfaiate.com.br',                'WordPress'],
            ['FLY SP',                                                    'flysp.com.br',                          'HTML'],
            ['FOLIA E FANTASIA BUFFET',                                   'foliaefantasia.com.br',                 'HTML'],
            ['FUJI FERRAMENTAS',                                          'fuji.com.br',                           'HTML'],
            ['GRUPO FORTE BRASIL',                                        'gfortebrasil.com.br',                   'WordPress'],
            ['GUARULHOS REDES E TELAS',                                   'guarulhosredesetelas.com.br',           'site não é nosso'],
            ['HENRIPARK',                                                 'henriparkestacionamento.com.br',        'WordPress'],
            ['HERIMAR BOTÕES',                                            'herimar.com.br',                        'WordPress'],
            ['HIDRO TEC DESENTUPIDORA',                                   'hidrotecdesentupimento.com.br',         'HTML'],
            ['HJ COMÉRCIO DE FERRAMENTAS',                                'hjferramentasdecorte.com.br',           'HTML'],
            ['HOSPEDARIA IPIRANGA',                                       'hospedariaipiranga.com.br',             'HTML'],
            ['HOTEL MAR & ONDA',                                          'hotelmareonda.com.br',                  'HTML'],
            ['IMPERIAL INSTALAÇÕES DE GÁS',                               '',                                      'HTML'],
            ['INSTITUTO ORTOPÉDICO ITARARÉ',                              'institutoortopedicoitarare.com.br',     'HTML'],
            ['INTER BRASIL ALIMENTOS',                                    'interbrasilalimentos.com.br',           'HTML'],
            ['INTERATIVA ENGENHARIA',                                     'interativaengenharia.com.br',           'HTML'],
            ['IPJO',                                                      'jardimdasoliveiras.org.br',             'HTML'],
            ['ITARAÍ METALURGIA',                                         'itarai.com.br',                         'WordPress'],
            ['J INOX EQUIPAMENTOS PARA COZINHA',                          'jinoxcozinhas.com.br',                  'HTML'],
            ['JOÃOZINHO DESPACHANTE',                                     'joaozinhodespachante.com.br',           'HTML'],
            ['JOFF PLAS ESQUADRIAS METÁLICAS',                            'joffplas.com.br',                       'HTML'],
            ['JOMAR BOTÕES',                                              'jomarbotoes.com.br',                    'HTML'],
            ['JW PLANEJADOS',                                             'jwplanejados.com.br',                   'HTML'],
            ['KAIZER CONEXÕES',                                           'kaizerconexoes.com.br',                 'HTML'],
            ['KD JUNTAS',                                                 'kdjuntas.com.br',                       'WordPress'],
            ['KGF EQUIPAMENTOS HIDRÁULICOS LTDA',                         'kgfequipamentos.com.br',                'HTML'],
            ['L A ARQUITHETURA (CANCELADO)',                               '',                                      'HTML'],
            ['LAJES PIOLI',                                               'lajespioli.com.br',                     'HTML'],
            ['LAVANDERIA LETÁCIA',                                        'lavanderialeticia.com.br',              ''],
            ['LE HOUSE HOSTEL',                                           'lehousehostel.com.br',                  'WordPress'],
            ['NUQUE ARQUITETURA',                                         'nuquearq.com.br',                       'HTML'],
            ['LUBRIMATIC',                                                'lubrimatic.com.br',                     'WordPress'],
            ['LUCCAS MENDES CORTES (CANCELADO)',                          '',                                      'WordPress'],
            ['MACTECEMP',                                                 'mactecemp.com.br',                      'WordPress'],
            ['MADEIREIRA MORRO DOCE',                                     'tudoparaseutelhado.com.br',             ''],
            ['MADEIREIRA PAULISTINHA',                                    'madeireirapaulistinha.com.br',          'WordPress'],
            ['MADEIREIRA RIO VERDE',                                      'madeireirarioverde.com.br',             'HTML'],
            ['MADEIREIRA RODRIGUES',                                      'madeireirarodrigues.com.br',            'HTML'],
            ['MADEIREIRA SÃO JOÃO',                                       'madsaojoao.com.br',                     'WordPress'],
            ['MADEIREIRA VITÓRIA',                                        '',                                      'HTML'],
            ['MADEIREIRA VITORIA SOL NASCENTE',                           '',                                      'WordPress'],
            ['MANANCIAL PISCINA',                                         'www.manancialpiscina.com.br',           'HTML'],
            ['MAPA DO PANO DECORAÇÕES',                                   'www.mapadopano.com.br',                 'HTML'],
            ['MARIDO DE ALUGUEL SOS',                                     'maridodealuguelsos.com.br',             'HTML'],
            ['MARTA ALVES VITAL',                                         '',                                      'HTML'],
            ['MASTER LAB',                                                '',                                      'HTML'],
            ['MAURE TORRES',                                              'martavitalpsicologa.com.br',            'WordPress'],
            ['MED NEXO',                                                  'masterlaboficial.com.br',               'WordPress'],
            ['MERCADÃO',                                                  'mercadaco.com.br',                      'HTML'],
            ['METROVIÁRIOS DE CRISTO',                                    'metroviariosdecristo.org.br',           'HTML'],
            ['MODAL LOCAÇÃO DE MÁQUINAS E EQUIPAMENTOS (CANCELADO)',      '',                                      'HTML'],
            ['MVS ACÚSTICA',                                              'mvsacustica.com.br',                    'HTML'],
            ['NOVA ABC COLETA DE RESÍDUOS LTDA',                          '',                                      ''],
            ['NOVA PINTURA',                                              '',                                      'WordPress'],
            ['NOVA SADINI DEDETIZADORA',                                  '',                                      ''],
            ['NUBIA SUSHI',                                               'nubiasushibrasil.com.br',               'site não é nosso'],
            ['ODONT - ODONTOLOGIA ESPECIALIZADA',                         '',                                      'HTML'],
            ['ORLA SERVIÇOS',                                             'orlaservicos.com.br',                   'HTML'],
            ['PÃES NATURALLIS',                                           'nubiasushibrasil.com.br',               'WordPress'],
            ['PAIVA SECURITY',                                            '',                                      ''],
            ['PASIMFER',                                                  'https://pasimfer.com.br',               'WordPress'],
            ['PENSKE',                                                    '',                                      'HTML'],
            ['PGP INDÚSTRIA DE PLÁSTICOS',                                'plasticosnogueira.com.br',              ''],
            ['PIZZARIA LA FAMIGLIAS',                                     '',                                      'WordPress'],
            ['PLÁSTICOS NOGUEIRA',                                        '',                                      'site não é nosso'],
            ['POLICLÍNICA + HUMANA',                                      'policlinicamaishumana.com.br',          'HTML'],
            ['PONTOPAR',                                                  '',                                      'HTML'],
            ['PRIETTO DIVISÓRIAS',                                        'priettodivisorias.com.br',              'HTML'],
            ['PURIFICADOR BRASTEMP',                                      'purificadorbrastempsp.com.br',          'HTML'],
            ['R&GO CONSTRUCTION COMPANY',                                 'rgoconstructioncompany.com',            'HTML'],
            ['R2AR ENGENHARIA (TRATAR DO CASO)',                          'r2ar.com.br',                           'WordPress'],
            ['RAFAEL SHIGUEKI GOSHI FORTE',                               'rafaelgoshiforte.com.br',               'WordPress'],
            ['RENOVAPLASTIC INDÚSTRIA E COMÉRCIO DE PLÁSTICOS',           'renovaplastic.com.br',                  'HTML'],
            ['REVIZZI LUBRIFICANTES E BATERIAS',                          'revizzibaterias.com.br',                'WordPress'],
            ['ROLFIX ROLAMENTOS E FIXAÇÃO',                               'rolfixrolamentos.com.br',               'HTML'],
            ['ROLLMAQ',                                                   'rollmaq.com.br',                        'HTML'],
            ['ROSTEC',                                                    'rostec.com.br',                         'WordPress'],
            ['S&F MANUTENÇÃO (INADIMPLENTE / CANCELADO)',                 'sefmanutencao.com.br',                  'HTML'],
            ['SALUTAR MEDICINA E SAÚDE',                                  'salutarmedicinaesaude.com.br',          'HTML'],
            ['SANTOS REPAROS',                                            'santosreparos.com',                     'HTML'],
            ['SCAP TOTAL AUTO CENTER',                                    'scaptotal.com.br',                      ''],
            ['SHIOZAWA SHIATSU MASSAGEM',                                 '',                                      ''],
            ['SILICONES PAULISTA',                                        'siliconespaulista.com.br',              'WordPress'],
            ['SIMETRIA CORTINAS E PERSIANAS',                             'simetriacortinas.com.br',               'HTML'],
        ];
    }
}
