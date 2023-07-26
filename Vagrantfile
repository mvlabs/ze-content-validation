Vagrant.configure("2") do |config|
  config.vm.box = "debian/bullseye64"
  config.ssh.forward_agent = true

  config.vm.provision "shell", inline: <<-SHELL
    # Add sury repo
    apt-get install -y apt-transport-https lsb-release ca-certificates
    wget -q -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
    sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
    apt-get update

    # Install PHP and git
    apt-get install -y php8.1-cli php8.1-xml php8.1-mbstring php8.1-zip php8.1-curl php8.1-dom php8.1-xdebug
    apt-get install -y curl git unzip yamllint

    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
  SHELL

  config.vm.provision "shell", privileged: false, inline: <<-SHELL
    # Switch to /vagrant and install packages
    cd /vagrant && composer install
  SHELL
end
