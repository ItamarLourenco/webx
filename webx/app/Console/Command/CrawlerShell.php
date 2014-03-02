<?php
class CrawlerShell extends AppShell {
    public function main() {
    	$this->stdout->styles('barras', array('text' => 'cyan'));
    	$this->stdout->styles('log', array('text'=> 'blue'));
    	$this->stdout->styles('ok', array('text'=> 'green'));

    	$this->out('<barras>--------------------------------------------------------------------------------------------------------------------------</barras>');
    	$this->out("INICIANDO Crawler");
    	$this->out('<barras>--------------------------------------------------------------------------------------------------------------------------</barras>');
    	
    	$this->loadModel('Url');
		$this->loadModel('Email');

		//Iniciando While
		do{
			$this->out('<barras>--------------------------------------------------------------------------------------------------------------------------</barras>');
			$this->out('<barras>--------------------------------------------------------------------------------------------------------------------------</barras>');

			//Pegar URL com visited = no
			$url = $this->Url->find('all', array(
				'conditions' => array('Url.visited' => 'no')
			));

			//Ler todas as URLS
			$this->crawler($url);



	    }while(!empty($url)); //Se nao achar nenhum dados no db = no para o loop

    	$this->out('<barras>--------------------------------------------------------------------------------------------------------------------------</barras>');
    	$this->out("FIM Crawler");
    	$this->out('<barras>--------------------------------------------------------------------------------------------------------------------------</barras>');
    }

    private function crawler($urls){
    	foreach ($urls as $key => $url) {	
    	
	    	$sUrl = $url['Url']['url'];  	
	    	
			$this->out("URL -> <log>$sUrl</log> \n");
			//Lendo URL
			$this->lendoUrl($sUrl);
			
			//Trocando vesited para yes
			$this->Url->save(array('id' => $url['Url']['id'], 'visited' => 'yes'));

			$this->out('<barras>--------------------------------------------------------------------------------------------------------------------------</barras>');
			$this->out('TOTAL DE URL NO BANCO -> <log>'.$this->Url->find('count').'</log>');
			$this->out('TOTAL DE URL NO BANCO PARA SER LIDA -> <log>'.$this->Url->find('count', array('conditions' => array('Url.visited' => 'no'))) .'</log>');
			$this->out('TOTAL DE EMAIL NO BANCO -> <log>'.$this->Email->find('count') .'</log>');
			$this->out('<barras>--------------------------------------------------------------------------------------------------------------------------</barras>');
			$this->out('<barras>--------------------------------------------------------------------------------------------------------------------------</barras>');
		}
    }

    private function lendoUrl($url)
    {    
    	//Inicionado leitura da página
    	try{
	    	$this->out("INICIANDO LEITURA \n");
	    	App::uses('HttpSocket', 'Network/Http');
			$HttpSocket = new HttpSocket();
			$resultado_url = $HttpSocket->get(urldecode($url));
			$this->out("<ok>SUCESSO!</ok>  \n");
			$this->out("EXTRAINDO URLS  \n");
			$this->saveUrls($resultado_url, $url);
		}catch(Exception $e){
			$this->out("ERRO AO LER: <error>{$e->getMessage()} </error>  \n");
		}


    }


    private function saveUrls($resultado_url, $url){
    	//Extraindo URLS 
    	preg_match_all('/<a href=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?>/i', $resultado_url, $urls);
    	$urls = $urls[1];


    	//Pegando informacao da URL
    	$host = $this->getHost($url);   	

    	//Contagem
    	$totalAdd = 0;
    	$totalNaoAdd = 0;
    	$totalError = 0;


    	//Arrumando link nao absolutos
    	foreach ($urls as $key => $value) {
			$url = parse_url($value);
			if(isset($url['scheme']) && isset($url['host'])){
				$urlArr = $url['scheme']."://".$url['host'];
				if(isset($url['path'])){
					$urlArr .= $url['path'];
				}
				if(isset($url['query'])){
					$urlArr .= $url['query'];
				}
			}else{
				$urlArr = $host;
				if(isset($url['path'])){
					$urlArr = $host.$url['path'];
				}

				if(isset($url['query'])){
					$urlArr .= '?'.$url['query'];
				}
			}

			//Validacao de URL
			if(filter_var($urlArr, FILTER_VALIDATE_URL)){
				//Verificar se a URL já existe
				if($this->Url->saveUrl($urlArr) == 0){
					//Salvando no DB
					if($this->Url->saveAll(array('url' => $urlArr))){
						$totalAdd++;
					}else{
						$totalError++;
					}
				}else{
					$totalNaoAdd++;
				}
			}
		}

		$this->out("TOTAL DE URL ADICIONADAS -> <log>$totalAdd</log> :: TOTAL DE URL JÁ EXISTENTE -> <log>$totalNaoAdd</log> :: TOTAL DE ERROS -> <log>$totalError</log> \n");

		$this->out("EXTRAINDO EMAILS \n");
		$this->saveEmail($resultado_url, $url);
    }


    private function saveEmail($resultado_url, $url){
    	//Extraindo Emails
    	preg_match_all('/\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $resultado_url, $email);

    	//Contagem
    	$totalAdd = 0;
    	$totalNaoAdd = 0;

    	foreach ($email[0] as $key => $value) {
    		//Validando email
    		if(filter_var($value, FILTER_VALIDATE_EMAIL)){
    			//Verificando se existe no db
    			if($this->Email->saveEmail($value) == 0){    				
    				if($this->Email->saveAll(array('email' => $value))){
    					$this->out("EMAIL SALVO COM SUCESSO -> $value \n");
    					$totalAdd++;
    				}    				
    			}else{
    				$this->out("<error>EMAIL JÁ EXISTE -> $value </error>");
    				$totalNaoAdd++;
    			}
    		}
    	}

    	$this->out("TOTAL DE EMAIL ADICIONADAS -> <log>$totalAdd</log> :: TOTAL DE EMAIL JÁ EXISTENTE -> <log>$totalNaoAdd</log> \n");

    }




    private function getHost($url){
    	$url = parse_url($url);
    	if(isset($url['scheme']) && isset($url['host'])){
    		return $url['scheme']."://".$url['host'];
    	}else{
    		return null;
    	}
    }


}
?>