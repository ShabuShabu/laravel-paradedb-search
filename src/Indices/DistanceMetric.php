<?php

namespace ShabuShabu\ParadeDB\Indices;

enum DistanceMetric: string
{
    case denseL2 = 'vector_l2_ops';
    case denseInnerProduct = 'vector_ip_ops';
    case denseCosine = 'vector_cosine_ops';

    case sparseL2 = 'sparsevec_l2_ops';
    case sparseInnerProduct = 'sparsevec_ip_ops';
    case sparseCosine = 'sparsevec_cosine_ops';
}
