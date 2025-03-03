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

MESSAGE="
	grub device = ${GRUB_DEVICE}
	swap device = ${SWAP_DEVICE}
	root device = ${ROOT_DEVICE}
	mount point = ${MOUNT_POINT}
	release name = ${RELEASE_NAME}
"
echo "${MESSAGE}"

read -p "Is correct configuration? (yes) " ANSWER
if test "${ANSWER}" != "yes"
	then
	exit 255
fi

exit 0
