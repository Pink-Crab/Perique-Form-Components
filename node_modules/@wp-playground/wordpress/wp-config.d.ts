import type { UniversalPHP } from '@php-wasm/universal';
/**
 * Ensures that the "wp-config.php" file exists and required constants are defined.
 *
 * When a required constant is missing, it will be defined with a default value.
 *
 * @param php          The PHP instance.
 * @param documentRoot The path to the document root.
 */
export declare function ensureWpConfig(php: UniversalPHP, documentRoot: string): Promise<void>;
/**
 * Defines constants in a WordPress "wp-config.php" file.
 *
 * This function modifies the "wp-config.php" file to define the given constants.
 *
 *   1. When a constant is already defined, the definition will be updated.
 * 	 2. When a constant is not defined, it will be added in an appropriate
 *      location within the file (typically before the "stop editing" line).
 *
 * @param php          The PHP instance.
 * @param wpConfigPath The path to the "wp-config.php" file.
 * @param constants    The constants to define.
 */
export declare function defineWpConfigConstants(php: UniversalPHP, wpConfigPath: string, constants: Record<string, unknown>): Promise<void>;
