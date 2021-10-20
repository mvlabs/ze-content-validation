Vagrant.configure("2") do |config|
  config.vm.box = "debian/contrib-buster64"
  config.ssh.forward_agent = true

  config.vm.provision "shell", inline: <<-SHELL
    # Add sury repo
    apt-get install -y apt-transport-https lsb-release ca-certificates
    wget -q -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
    sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
    apt-get update

    # Install PHP and git
    apt-get install -y php7.4-cli php7.4-xml php7.4-mbstring php7.4-zip php7.4-curl php7.4-dom php7.4-json php7.4-xdebug git unzip

    cd /usr/src
    sudo apt-get install -y curl
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
  SHELL

  config.vm.provision "shell", privileged: false, inline: <<-SHELL
    # Switch to /vagrant and install packages
    cd /vagrant && composer install
  SHELL
end
