import type { PHP } from './php';
/**
 * Mounts directories from one PHP instance's filesystem into another using PROXYFS.
 *
 * This enables file sharing between PHP instances without duplicating the files in memory.
 * For example, mounting /wordpress from the parent instance into a child worker allows
 * both to access the same WordPress installation without copying the entire directory.
 *
 * The function automatically patches PROXYFS with mmap support before mounting, ensuring
 * libraries like ICU can memory-map data files through the proxied filesystem.
 *
 * Mounts are registered via php.mount() so they survive runtime rotation.
 * When the replica's WASM module is hot-swapped, hotSwapPHPRuntime()
 * re-applies these mount handlers on the fresh module.
 *
 * @param sourceOfTruth - The PHP instance containing the original files
 * @param replica - The PHP instance that will access files through PROXYFS
 * @param paths - Absolute paths to mount (e.g., ['/wordpress', '/internal/shared'])
 */
export declare function proxyFileSystem(sourceOfTruth: PHP, replica: PHP, paths: string[]): Promise<void>;
/**
 * Answers whether the given path is to a shared filesystem.
 *
 * @param sourceOfTruth - The PHP instance that is the source of truth.
 * @param path - The path to check.
 * @returns True if the path is to a shared filesystem, false otherwise.
 */
export declare function isPathToSharedFS(sourceOfTruth: PHP, path: string): any;
