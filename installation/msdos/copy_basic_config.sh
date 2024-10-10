#!/bin/sh

### Functions
#{{{

error ()
{
	/usr/bin/echo -e -n "\nERROR: ${BASENAME} - ${1}\n" >&2
	return 255
}

notice ()
{
	/usr/bin/echo -e -n "\nNOTICE: ${BASENAME} - ${1}\n"
	return 0
}

run ()
{
	COMMAND="${*}"
	echo "\n> ${COMMAND}"
	${COMMAND}
	if test ${?} -ne 0
	then
		error "Running the command failed with an error"
		exit 255
	fi
	return 0
}

#}}}

### Initialization
# {{{

FILE=`/usr/bin/realpath $0`
DIR=`/usr/bin/dirname ${FILE}`
BASENAME=`/usr/bin/basename ${FILE}`
ERROR=0

HELP="
Description: Copy basic config files and next installation step scripts
Usage: ${BASENAME} <config_file/path>
"
if test "${1}" = "-h" -o "${1}" = "--help"
then
	/usr/bin/echo "${HELP}"
	exit 0
fi

# }}}

if test -z "${1}"
	then
	error "Path to config file is not set in command line"
	exit 255
fi
CONFIG_FILE="${1}"
. ${CONFIG_FILE}

### fstab
# {{{

notice "Create ${MOUNT_POINT}/etc/fstab"

export SWAP_UUID=`/usr/bin/lsblk -n -o UUID ${SWAP_DEVICE}`
export ROOT_UUID=`/usr/bin/lsblk -n -o UUID ${ROOT_DEVICE}`

TEXT=$(/usr/bin/cat ${DIR}/etc/fstab)
if test ${?} -ne 0
	then
	error "Can't cat ${MOUNT_POINT}/etc/fstab"
	exit 255
fi

TEXT=$(echo "${TEXT}" | /usr/bin/envsubst)
if test ${?} -ne 0
	then
	error "Can't envsubst text of fstab"
	exit 255
fi

TEXT=$(echo "${TEXT}" > ${MOUNT_POINT}/etc/fstab)
if test ${?} -ne 0
	then
	error "Can't put text to ${MOUNT_POINT}/etc/fstab"
	exit 255
fi

# }}}

### sources.list
# {{{

notice "Create ${MOUNT_POINT}/etc/apt/sources.list"

TEXT=$(/usr/bin/cat ${DIR}/etc/sources.list)
if test ${?} -ne 0
	then
	error "Can't cat ${DIR}/etc/sources.list"
	exit 255
fi

export RELEASE_NAME="${RELEASE_NAME}"
TEXT=$(echo "${TEXT}" | /usr/bin/envsubst)
if test ${?} -ne 0
	then
	error "Can't envsubst text of sources.list"
	exit 255
fi

echo "${TEXT}" > ${MOUNT_POINT}/etc/apt/sources.list
if test ${?} -ne 0
	then
	error "Can't save source.list text in mount point"
	exit 255
fi

# }}}

### installation_before_reboot
# {{{

notice "Create ${MOUNT_POINT}/usr/local/sbin/installation_before_reboot"

export GRUB_DEVICE="${GRUB_DEVICE}"

TEXT=$(/usr/bin/cat ${DIR}/sbin/installation_before_reboot)
if test ${?} -ne 0
	then
	error "Can't cat ${DIR}/sbin/installation_before_reboot"
	exit 255
fi

TEXT=$(echo "${TEXT}" | /usr/bin/envsubst)
if test ${?} -ne 0
	then
	error "Can't envsubst text of installation_before_reboot"
	exit 255
fi

TEXT=$(echo "${TEXT}" > ${MOUNT_POINT}/usr/local/sbin/installation_before_reboot)
if test ${?} -ne 0
	then
	error "Can't put text to ${MOUNT_POINT}/usr/local/sbin/installation_before_reboot"
	exit 255
fi

run /usr/bin/chmod 755 ${MOUNT_POINT}/usr/local/sbin/installation_before_reboot

# }}}

run /usr/bin/cp ${DIR}/sbin/installation_after_reboot ${MOUNT_POINT}/usr/local/sbin/
run /usr/bin/chmod 755 ${MOUNT_POINT}/usr/local/sbin/installation_after_reboot

run /usr/bin/cp ${DIR}/etc/.bashrc ${MOUNT_POINT}/root/
run /usr/bin/cp ${DIR}/etc/.vimrc ${MOUNT_POINT}/root/
run /usr/bin/cp ${DIR}/etc/hdparm.conf ${MOUNT_POINT}/usr/local/etc/

exit 0

