import * as esbuild from 'esbuild'

await esbuild.build({
  entryPoints: ['test_pagume_bug.mjs'],
  bundle: true,
  platform: 'node',
  outfile: 'test_pagume_bug_bundled.cjs',
  target: ['es2020'],
  format: 'cjs',
})
