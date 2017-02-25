<?php
namespace Credio;
/**
 * @brief Define %Credio Error codes
 *
 */
abstract class ErrorCodes
{
    const NO_ERROR = 0x00000000;///< no error.
    const LICENSE_EXCEEDED = 0x00000001;///< exceeding license limits.
    const INVALID_REQUEST = 0x00000010;///< invalid request.
    const INVALID_PATH = 0x00000011;///< invalid path.
    const INVALID_REGEX = 0x00000012;///< invalid regular expression.
    const GENERIC_NAME_TOOLONG = 0x00000013;///< name is to long.
    const GENERIC_NAME_INVALID = 0x00000014;///< name is not valid.
    const GENERIC_NOT_FOUND = 0x00000015;///< object not found.
    const TOKEN_NOT_FOUND = 0x00000101;///< token not found.
    const PERMISSION_DENIED = 0x00000102;///< access violation.
    const DOMAIN_NOT_FOUND = 0x00000103;///< domain not found.
    const INVALID_CREDENTIALS = 0x00000104;///< invalid credentials.
    const PAMPROFILE_NOT_FOUND = 0x00000105;///< nss profile not found.
    const RESOURCE_INUSE = 0x00000106;///< in use within other resources
    const NETCLIENT_NOT_FOUND = 0x00000107;///< network client not found.
    const USER_NOT_FOUND = 0x00000108;///< user not found.
    const CONN_NOTALLOWED_FOR_DOMAIN = 0x00000109;///< connection not allowed for domain.
    const INVALID_OLD_PASSWORD = 0x00000201;///< invalid old password.
    const REMOVING_OWN_ACCOUNT = 0x00000202;///< removing your own account is not allowed.
    const CHANGING_OWN_PASS_WOLD = 0x00000203;///< changing own password, without old password is not permitted. (use userEdit instead).
    const DOMAIN_DELETE_ROOT = 0x00000301;///< root domain cannot be deleted.
    const DOMAIN_NOT_EMPTY = 0x00000302;///< domain must not contain any subdomains.
    const DOMAIN_PARENT_NOT_FOUND = 0x00000303;///< domain parent not found.
    const DOMAIN_NAME_EXISTS = 0x00000304;///< domain with this name already exists.
    const DOMAIN_CHILD_OF_CHILD = 0x00000305;///< domain cannot become child of his child.
    const DOMAIN_CHILD_OF_SELF = 0x00000306;///< domain cannot become child of his self.
    const DOMAIN_INIT_FAILED = 0x00000307;///< domain initialization failed.
    const DOMAIN_DELETE_FROM_WITHIN = 0x00000308;///< domain cannot be deleted from account within the domain.
    const DOMAIN_NAME_TOOLONG = 0x00000309;///< domain name is to long.
    const DOMAIN_NAME_NOT_VALID = 0x00000310;///< domain name is not valid.
    const RESOURCE_NOT_FOUND = 0x00000401;///< resource not found.
    const RESOURCE_PARENT_NOT_FOUND = 0x00000402;///< resource parent not found.
    const RESOURCE_NAME_EXISTS = 0x00000403;///< resource with this name already exists.
    const RESOURCE_MODIFY_VIOLATION = 0x00000404;///< system resource cannot be deleted, renamed or change parent.
    const RESOURCE_CHILD_OF_CHILD = 0x00000405;///< resource cannot become child of his child.
    const RESOURCE_CHILD_OF_SELF = 0x00000406;///< resource cannot become child of his self.
    const USER_NAME_EXISTS = 0x00000501;///< user with this username already exists.
    const USER_USERNAME_TOOLONG = 0x00000502;///< username is too long.
    const USER_USERNAME_NOT_VALID = 0x00000503;///< username is not valid.
    const GROUP_NOT_FOUND = 0x00000601;///< group not found.
    const GROUP_NAME_EXISTS = 0x00000602;///< group with that name already exists.
    const GROUP_CHILD_OF_CHILD = 0x00000603;///< group A cannot become member of group B becouse group B is already a member of group A. (loop)
    const GROUP_CHILD_OF_SELF = 0x00000604;///< group cannot become member of his self.
    const GROUP_MEMBEROF_NOT_FOUND = 0x00000605;///< group member not found.
    const PAMPROFILE_NAME_EXISTS = 0x00000701;///< nss profile with this name already exists.
    const PAMPROFILE_CHILD_OF_CHILD = 0x00000702;///< NSS profile A cannot become member of NSS profile B becouse B is already member of A. (loop)
    const ATTRIBUTE_NOT_FOUND = 0x00000801;///< attribute not found.
    const ATTRIBUTE_NAME_EXISTS = 0x00000802;///< attribute whit this name already exists.
    const ATTRIBUTE_IMUNE_DEL_VIOLATION = 0x00000803;///< attribute is immune for deletion.
}

?>