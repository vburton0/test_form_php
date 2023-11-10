<?php
require_once 'vendor/autoload.php'; 
use jamesiarmes\PhpEws\Client;
use jamesiarmes\PhpEws\Request;
use jamesiarmes\PhpEws\Type;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$nom = $_POST["nom"];
	$prenom = $_POST["prenom"];
	$mail = $_POST["mail"];
	$code_postal = $_POST["Code_Postal"];
	$ville = $_POST["Ville"];
	$tel = $_POST["tel"];
	$date = $_POST["date"];
	$sexe = $_POST["sexe"];
	$annonce = $_POST["annonce"];
	$comment = $_POST["message"];
	$reponseOuiNon = isset($_POST['reponse_oui_non']) ? $_POST['reponse_oui_non'] : '';

	// Mail body
	$message_form = "Nom: $nom\n";
	$message_form .= "Prénom: $prenom\n";
	$message_form .= "Mail: $mail\n";
	$message_form .= "Code Postal: $code_postal\n";
	$message_form .= "Ville: $ville\n";
	$message_form .= "Téléphone: $tel\n";
	$message_form .= "Date de Naissance: $date\n";
	$message_form .= "Sexe: $sexe\n";
	$message_form .= "Annonce choisie: $annonce\n";
	$message_form .= "Message: $comment\n\n";
	$message_form .= "Connais quelqu'un dans l'etude: $reponseOuiNon\n";
	
	
	$uploadOk = 0;
	// CV upload
	if (isset($_FILES["CV"]) && !empty($_FILES["CV"])) {
		$cvFile = $_FILES["CV"];
		// Vérifiez si le fichier a été téléchargé sans erreur
		if ($cvFile["error"] == 0) {
			// Construisez le chemin complet du fichier cible
			$targetDirectory = "uploads/";
			$targetFile = $targetDirectory . basename($cvFile["name"]);
			$uploadOk = 1;
			
			// Obtenez l'extension du fichier
			$cvFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
			
			// ... Reste du code pour le traitement du fichier
			if ($_FILES["CV"]["size"] > 500000) {
				echo "Désolé, le fichier CV est trop volumineux.";
				$uploadOk = 0;
			}
		
			if ($cvFileType != "pdf" && $cvFileType != "doc" && $cvFileType != "docx") {
				echo "Désolé, seuls les fichiers PDF, DOC et DOCX sont autorisés pour le CV.";
				$uploadOk = 0;
			}
		} else {
			echo "Erreur lors du téléchargement du fichier CV.";
		}
	} else {
		echo "Aucun fichier CV n'a été téléchargé.";
	}

	// $targetDirectory = "uploads/";
	// $targetFile = $targetDirectory . basename($_FILES["CV"]["name"]);
	// $cvFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));


	if ($uploadOk == 0) {
		echo "Désolé, votre Fichier n'a pas été envoyé.";
	} else {
		if (move_uploaded_file($_FILES["CV"]["tmp_name"], $targetFile)) {
			$message_form .= "CV est attaché.";

			// Send mail
			// $to = "victor.burton@leroy-partners.com";  // Remplacez par votre adresse e-mail //hrh@leroy-partners.be
			// $subject = "Nouvelle candidature de $nom $prenom";
			// $headers = "From: victor.burton@leroy-partners.com";  // Remplacez par votre adresse e-mail ou utilisez une adresse valide
			// try{
			// 	//mail($to, $subject, $message, $headers);
			// 	echo "Votre formulaire a été envoyé avec succès.";
			// } catch (Exception $e) {
			// 	echo "Une erreur s'est produite lors de l'envoi de vote formulaire";}
		}else {
			echo "Une erreur s'est produite lors de l'envoi du fichier CV.";
		}
		
		$server = 'mail.leroy-partners.be:443'; // Remplacez cela par l'URL de votre serveur EWS
		$username = 'ASSOCLEROY\informatique';
		$password = 'informatique';
		$version = Client::VERSION_2016;
			// Initialiser le client EWS
		$client = new Client($server, $username, $password, $version);

		// Créer un nouvel e-mail
		$message = new Type\MessageType();
		$message->Subject = 'Postulation sur le site';
		$message->Body = new Type\BodyType();
		$message->Body->BodyType = 'Text';
		$message->Body->_ = $message_form;

		// Spécifier manuellement un ID (exemple seulement)
		$message->ItemId = new Type\ItemIdType();
		$message->ItemId->Id = 15;
		$message->ItemId->ChangeKey = 4;

		// Ajouter la pièce jointe PDF
		// $attachment = new Type\FileAttachmentType();
		// $attachment->Content = file_get_contents($targetFile); // Remplacez cela par le chemin vers votre fichier PDF 
		// $attachment->Name = $targetFile;
		// $message->Attachments = [$attachment];

		// Destinataire
		$to = new Type\EmailAddressType();
		$to->EmailAddress = 'victor.burton@leroy-partners.be'; // Remplacez cela par l'adresse e-mail du destinataire
		$message->ToRecipients = [$to];
		// Envoyer l'e-mail
		$request = new Request\CreateItemType();
		$request->Items = [$message];
		$request->MessageDisposition = 'SendOnly';

		// echo "ItemId" . $request->;

		try {
			var_dump($request);
			// echo "\n coucou\n";
			$response = $client->CreateItem($request);

			if ($response->ResponseMessages->CreateItemResponseMessage[0]->ResponseClass == 'Success') {
				echo 'E-mail envoyé avec succès!';
			} else {
				echo 'Erreur lors de l\'envoi de l\'e-mail: ' . $response->ResponseMessages->CreateItemResponseMessage[0]->MessageText;
			}
		} catch (Exception $e) {
			echo 'Erreur: ' . $e->getMessage();
		}    
	}
} else {
	echo "Une erreur s'est produite. Veuillez réessayer.";
}
?>
