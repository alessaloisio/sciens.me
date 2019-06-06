<?php 

class Form{

	public $controller;
	public $errors;

	public function __construct($controller){
		$this->controller = $controller;
	}

	public function input($name, $label, $options=array()){
		// Gérer les erreurs
		$error = '';
		$classError = '';
		if(isset($this->errors[$name])){
			$error = $this->errors[$name];
			$classError = ' error';
		}

		// Récupérer les valeurs
		if(!isset($this->controller->request->data->$name)){
			$value = '';
		}else{
			$value = $this->controller->request->data->$name;
		}

		// Bouton d'envoi
		if($name == 'submit'){
			return '<input type="submit" value="'.$label.'">';
		}

		// Type hidden
		if($label == 'hidden'){
			return '<input type="hidden" name="'.$name.'" value="'.$value.'" >';
		}


		$html =  '<div class="clearfix'.$classError.'">
					<label for="input'.$name.'">'.(is_array($label) ? $options['label'] : $label).'</label>
						<div class="input">';

		// Permet d'ajouter des options (id, class, rows, cols....)
		$attr = ' ';
		foreach ($options as $k => $v) {
			if($k != 'type'){
				$attr .= "$k=\"$v\" ";
			}
		}

		// Permet de mettre en place les différents types d'entrées.
		if(!isset($options['type'])){
			$html .= '<input type="text" id="input'.$name.'" name="'.$name.'" value="'.$value.'" '.$attr.'>';
		}elseif($options['type'] == 'textarea'){
			$html .= '<textarea id="input'.$name.'" name="'.$name.'" '.$attr.'>'.$value.'</textarea>';
		}elseif($options['type'] == 'checkbox'){
			$html .= '<input type="hidden" name="'.$name.'" value="0"><input type="checkbox" name="'.$name.'" value="1" '.(empty($value)?'':'checked').'>';
		}elseif($options['type'] == 'password'){
			$html .= '<input type="password" id="input'.$name.'" name="'.$name.'" value="'.$value.'" '.$attr.'>';
		}elseif($options['type'] == 'radio'){

			foreach ($label as $k => $v) {
				$html .= '<input type="radio" name="'.$name.'" value="'.$k.'">'.$v;
				
			}

		}


		if($error){
			$html .= '<span class="inline-error" style="color:#ff0000;">'.$error.'</span>';
		}
		
		$html .= '</div></div>';

		return $html;
	}
	
}

?>