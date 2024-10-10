# installation

Installation on a memory stick
==============================

	In the 'gparted' create msdos partition with two partitions 'swap' and 'root', don't forget set boot flag.
	Run the terminal.
sudo screen
cd /tmp
git clone https://github.com/ckpunmkug/tools.git
cd tools/msdos_installation

	Configure and start installation.
vim ./bookworm.conf
./start.sh ./bookworm.conf

	Now you must restart computer and boot from memory stick.
/usr/local/sbin/installation_after_reboot
reboot
	
	Basic desktop installation complete.

