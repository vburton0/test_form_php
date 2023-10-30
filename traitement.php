<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$nom = $_POST["nom"];
	$prenom = $_POST["prenom"];
	$mail = $_POST["mail"];
	$code_postal = $_POST["Code Postal"];
	$ville = $_POST["Ville"];
	$tel = $_POST["tel"];
	$date = $_POST["date"];
	$sexe = $_POST["sexe"];
	$annonce = $_POST["annonce"];
	$comment = $_POST["message"];

	// Construction du corps de l'e-mail
	$message = "Nom: $nom\n";
	$message .= "Prénom: $prenom\n";
	$message .= "Mail: $mail\n";
	$message .= "Code Postal: $code_postal\n";
	$message .= "Ville: $ville\n";
	$message .= "Téléphone: $tel\n";
	$message .= "Date de Naissance: $date\n";
	$message .= "Sexe: $sexe\n";
	$message .= "Annonce choisie: $annonce\n";
	$message .= "Message: $comment\n";


	// Traitement du fichier CV
	$targetDirectory = "uploads/";
	$targetFile = $targetDirectory . basename($_FILES["cv"]["name"]);
	$uploadOk = 1;
	$cvFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

	if ($_FILES["cv"]["size"] > 500000) {
		echo "Désolé, le fichier CV est trop volumineux.";
		$uploadOk = 0;
	}

	if ($cvFileType != "pdf" && $cvFileType != "doc" && $cvFileType != "docx") {
		echo "Désolé, seuls les fichiers PDF, DOC et DOCX sont autorisés pour le CV.";
		$uploadOk = 0;
	}

	if ($uploadOk == 0) {
		echo "Désolé, votre formulaire n'a pas été envoyé.";
	} else {
		if (move_uploaded_file($_FILES["cv"]["tmp_name"], $targetFile)) {
			$message .= "CV est attaché.";

			// Envoi de l'e-mail
			$to = "victor.burton@leroy-partners.com";  // Remplacez par votre adresse e-mail
			$subject = "Nouvelle candidature de $nom $prenom";
			$headers = "From: webmaster@Manon.Meunier@leroy-partners.be.com";  // Remplacez par votre adresse e-mail ou utilisez une adresse valide
			
			mail($to, $subject, $message, $headers);  // gestion des erreur try catch
			echo "Votre formulaire a été envoyé avec succès.";
		}else {
			echo "Une erreur s'est produite lors de l'envoi du fichier CV.";
		}
	}
} else {
	echo "Une erreur s'est produite. Veuillez réessayer.";
}
?>