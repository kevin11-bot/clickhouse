<?php

namespace Pioneers\ClickHouse\Enum;

enum EngineTypeEnum: string
{
    case MERGE_TREE = 'MergeTree';
    case REPLICATED_MERGE_TREE = 'ReplicatedMergeTree';
    case REPLACING_MERGE_TREE = 'ReplacingMergeTree';
    case SUMMING_MERGE_TREE = 'SummingMergeTree';
    case AGGREGATING_MERGE_TREE = 'AggregatingMergeTree';

    case TINY_LOG = 'TinyLog';
    case LOG = 'Log';

    case KAFKA = 'Kafka';
    case MYSQL = 'MySQL';
    case S3 = 'S3';

    case DISTRIBUTED = 'Distributed';
    case MEMORY = 'Memory';
}
