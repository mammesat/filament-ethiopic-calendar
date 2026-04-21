import * as esbuild from 'esbuild'

await esbuild.build({
  entryPoints: ['test_miyazya.mjs'],
  bundle: true,
  platform: 'node',
  outfile: 'test_miyazya_bundled.cjs',
  target: ['es2020'],
  format: 'cjs',
})
