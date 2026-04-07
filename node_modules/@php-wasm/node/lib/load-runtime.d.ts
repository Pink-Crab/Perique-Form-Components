import { type SupportedPHPVersion, type EmscriptenOptions, type FileLockManager } from '@php-wasm/universal';
import type { WasmUserSpaceAPI, WasmUserSpaceContext } from './wasm-user-space';
import { type XdebugOptions } from './extensions/xdebug/with-xdebug';
export interface PHPLoaderOptions {
    followSymlinks?: boolean;
    withXdebug?: boolean | XdebugOptions;
    withIntl?: boolean;
    withRedis?: boolean;
    withMemcached?: boolean;
}
export type PHPLoaderOptionsForNode = PHPLoaderOptions & {
    /**
     * A file lock manager to coordinate file locks between
     * multiple php-wasm instances and other OS processes.
     */
    fileLockManager?: FileLockManager;
    emscriptenOptions?: EmscriptenOptions & {
        /**
         * The process ID for the PHP runtime.
         *
         * This is used to distinguish between php-wasm processes for the
         * purpose of file locking and more informative trace messages.
         *
         * This ID is optional when running a single php-wasm process.
         */
        processId?: number;
        /**
         * Factory called during WASM initialization to create
         * user-space syscall implementations (flock, fcntl, etc.)
         * for a PHP process. Receives process context (PID,
         * constants, errno codes) and returns the bound syscall
         * functions.
         */
        bindUserSpace?: (userSpaceContext: WasmUserSpaceContext) => WasmUserSpaceAPI;
        /**
         * An optional function to collect trace messages.
         *
         * @param processId - The process ID of the PHP runtime.
         * @param format - A printf-style format string.
         * @param args - Arguments to the format string.
         */
        trace?: (processId: number, format: string, ...args: any[]) => void;
        /**
         * An optional path used to a real, native directory
         * to be mounted as the php-wasm /internal directory.
         */
        nativeInternalDirPath?: string;
    };
};
/**
 * Does what load() does, but synchronously returns
 * an object with the PHP instance and a promise that
 * resolves when the PHP instance is ready.
 *
 * @see load
 */
export declare function loadNodeRuntime(phpVersion: SupportedPHPVersion, options?: PHPLoaderOptionsForNode): Promise<number>;
