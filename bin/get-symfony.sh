#! /bin/bash
# Symfony local installer.


set -e

CLI_LATEST_VERSION_URL="https://get.symfony.com/cli/LATEST"
CLI_CONFIG_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
CLI_EXECUTABLE="symfony"
CLI_TMP_NAME="$CLI_EXECUTABLE-"`date +"%s"`
CLI_NAME="Symfony CLI"

function output {
    style_start=""
    style_end=""
    if [ "${2:-}" != "" ]; then
    case $2 in
        "success")
            style_start="\033[0;32m"
            style_end="\033[0m"
            ;;
        "error")
            style_start="\033[31;31m"
            style_end="\033[0m"
            ;;
        "info"|"warning")
            style_start="\033[33m"
            style_end="\033[39m"
            ;;
        "heading")
            style_start="\033[1;33m"
            style_end="\033[22;39m"
            ;;
    esac
    fi

    builtin echo -e "${style_start}${1}${style_end}"
}

output "${CLI_NAME} installer" "heading"

# Run environment checks.
output "\nEnvironment check" "heading"

# Check that cURL or wget is installed.
downloader=""
command -v curl >/dev/null 2>&1
if [ $? == 0 ]; then
    downloader="curl"
    output "  [*] cURL is installed" "success"
else
    command -v wget >/dev/null 2>&1
    if [ $? == 0 ]; then
        downloader="wget"
        output "  [*] wget is installed" "success"
    else
        output "  [ ] ERROR: cURL or wget is required for installation." "error"
        exit 1
    fi
fi

# Check that gzip is installed.
command -v gzip >/dev/null 2>&1
if [ $? == 0 ]; then
    output "  [*] Gzip is installed" "success"
else
    output "  [ ] ERROR: Gzip is required for installation." "error"
    exit 1
fi

# Check that Git is installed.
command -v git >/dev/null 2>&1
if [ $? == 0 ]; then
    output "  [*] Git is installed" "success"
else
    output "  [ ] Warning: Git will be needed." "warning"
fi

# The necessary checks have passed. Start downloading the right version.
output "\nDownload" "heading"

kernel=`uname -s 2>/dev/null || /usr/bin/uname -s`
case ${kernel} in
    "Linux"|"linux")
        kernel="linux"
        ;;
    "Darwin"|"darwin")
        kernel="darwin"
        ;;
    *)
        output "OS '${kernel}' not supported" "error"
        exit 1
        ;;
esac

machine=`uname -m 2>/dev/null || /usr/bin/uname -m`
if [ machine == "i386" ]; then
    machine="386"
else
    machine="amd64"
fi

platform="${kernel}_${machine}"

output "  Finding the latest version (platform: \"${platform}\")...";

case ${downloader} in
    "curl")
        latest_version=`curl --fail ${CLI_LATEST_VERSION_URL} -s`
        ;;
    "wget")
        latest_version=`wget -q ${CLI_LATEST_VERSION_URL} -O - 2>/dev/null`
        ;;
esac
if [ $? != 0 ]; then
    output "  Failed to download LATEST version file: ${CLI_LATEST_VERSION_URL}" "error"
    exit 1
fi

latest_url="https://github.com/symfony/cli/releases/download/v${latest_version}/symfony_${platform}.gz"
output "  Downloading version ${latest_version} (${latest_url})...";
case $downloader in
    "curl")
        curl --fail --location "${latest_url}" > "/tmp/${CLI_TMP_NAME}.gz"
        ;;
    "wget")
        wget -q --show-progress "${latest_url}" -O "/tmp/${CLI_TMP_NAME}.gz"
        ;;
esac

if [ $? != 0 ]; then
    output "  The download failed." "error"
    exit 1
fi

output "  Uncompress binary..."
gzip -d "/tmp/${CLI_TMP_NAME}.gz"

output "  Making the binary executable..."
chmod 755 "/tmp/${CLI_TMP_NAME}"

binary_dest="${CLI_CONFIG_DIR}"
if [ ! -d "${binary_dest}" ]; then
    mkdir -p "${binary_dest}"
    if [ $? != 0 ]; then
        binary_dest="."
    fi
fi

output "  Installing the binary into bin directory..."
mv "/tmp/${CLI_TMP_NAME}" "${binary_dest}/${CLI_EXECUTABLE}"
output "  The binary was saved to: ${binary_dest}/${CLI_EXECUTABLE}"
output "\nThe ${CLI_NAME} v${latest_version} was installed successfully!" "success"
exit 0

