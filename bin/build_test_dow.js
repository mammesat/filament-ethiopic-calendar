import * as esbuild from 'esbuild'

await esbuild.build({
  entryPoints: ['test_deterministic_dow.mjs'],
  bundle: true,
  platform: 'node',
  outfile: 'test_deterministic_dow_bundled.cjs',
  target: ['es2020'],
  format: 'cjs',
})
