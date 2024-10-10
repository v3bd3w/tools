#!/bin/sh

### Functions ##################################################################
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

### Initialization #############################################################
# {{{

FILE=`/usr/bin/realpath $0`
DIR=`/usr/bin/dirname ${FILE}`
BASENAME=`/usr/bin/basename ${FILE}`
ERROR=0

HELP="
Description: Aggregate execution of scripts
Usage: ${BASENAME} <config_file>
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

### Main #######################################################################
# {{{

run ${DIR}/load_config.sh ${CONFIG_FILE}
run ${DIR}/bootstrap_basic_system.sh ${CONFIG_FILE}
run ${DIR}/mount_virtual_filesystems.sh ${CONFIG_FILE}
run ${DIR}/copy_basic_config.sh ${CONFIG_FILE}
run ${DIR}/chroot_to_installation ${CONFIG_FILE}

# }}}

