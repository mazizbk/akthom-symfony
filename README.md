# akthom-symfony

## Vous pouvez installer le serveur AMQP (Advanced Message Queuing Protocol) RabbitMQ sur Ubuntu 22.10 en suivant ces étapes

## Ouvrez un terminal sur votre machine Ubuntu

## Mettez à jour les packages disponibles sur votre système

sudo apt update
sudo apt install rabbitmq-server

## Vérifiez que le service RabbitMQ est en cours d'exécution à l'aide de la commande systemctl

sudo systemctl status rabbitmq-server.service

## Si vous ne voyez pas la mention "active (running)" dans la sortie de la commande précédente, démarrez le service RabbitMQ en utilisant la commande systemctl suivante

sudo systemctl start rabbitmq-server.service

## Pour lancer cette commande bin/console messenger:consume async automatiquement au démarrage du serveur RabbitMQ, vous pouvez suivre les étapes ci-dessous

## Accédez au dossier /etc/systemd/system/ en tant que superutilisateur à l'aide de la commande sudo

sudo su
cd /etc/systemd/system/

## Créez un fichier service .service pour votre application avec la commande suivante

vi messenger.service

## Ajoutez le contenu suivant dans le fichier

[Unit]
Description=My Messenger Service
After=rabbitmq-server.service

[Service]
User=www-data
ExecStart=/usr/bin/env bin/console messenger:consume async
WorkingDirectory=/var/www/html/aktehom

[Install]
WantedBy=multi-user.target

## Dans la section « [Service] », vous devez remplacer "my_user_name" par le nom d'utilisateur qui est propriétaire des fichiers de l'application. Vous devez également spécifier le chemin absolu vers le répertoire où se trouve le fichier "console" et lancer la commande

## Enregistrez et fermez le fichier (Ctrl + X, puis Y pour confirmer l'enregistrement)

## Activez le service et vérifiez son statut

systemctl daemon-reload    # Pour recharger les services systemd
systemctl enable messenger.service   # Pour activer notre nouveau serivce
systemctl status messenger.service   # Pour vérifier le statut de service

## Redémarrez le serveur RabbitMQ pour vous assurer que le nouvel ajout fonctionne correctement

systemctl restart rabbitmq-server

## À partir de maintenant, chaque fois que le serveur RabbitMQ démarre, le service systemd que vous avez créé lancera automatiquement la commande bin/console messenger:consume async

## Pour lancer fscrawler en mode rest ouvrez votre éditeur de texte préféré et créez un nouveau fichier nommé fscrawler.service dans /etc/systemd/system/

## Ajoutez le contenu suivant dans le fichier fscrawler.service

[Unit]
Description=FSCrawler
After=network.target

[Service]
Type=simple
User=aziz
WorkingDirectory=/home/aziz/code/fscrawler
ExecStart=/home/aziz/code/fscrawler/bin/fscrawler pdf_aktehom --silent --loop 0 --rest

Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target

## Note: Assurez-vous de remplacer/home/aziz/code/fscrawler/bin/fscrawler/ par le chemin complet vers l'emplacement où se trouve votre fichier fscrawler

## Enregistrez et fermez le fichier.

## Pour activer le service, tapez la commande suivante dans le terminale:

sudo systemctl enable fscrawler.service

## Vous pouvez maintenant démarrer le service avec la commande suivante

sudo systemctl start fscrawler.service

## Vérifiez si le service est en cours d'exécution et ne rencontre aucune erreur en utilisant la commande suivante

sudo systemctl status fscrawler.service

## Si vous souhaitez arrêter le service, utilisez la commande

sudo systemctl stop fscrawler.service

## Et si vous souhaitez désactiver le service, vous pouvez utiliser la commande

sudo systemctl disable fscrawler.service
